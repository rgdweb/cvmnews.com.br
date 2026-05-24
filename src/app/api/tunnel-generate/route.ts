import { NextRequest, NextResponse } from 'next/server'

// ============================================================
// MODO LIMPO — Single-shot direto, sem efeito
// ============================================================
// Pipeline mais simples possível:
//   1. Descobrir tunnel URL
//   2. Upload audio de referencia
//   3. Enviar texto INTEIRO pro Gradio (single call)
//   4. Baixar audio gerado
//   5. Retornar
//
// SEM chunking, SEM ASR, SEM preprocess, SEM posprocess extra.
// Exatamente como o Gradio demo no localhost:7860 faz.
// ============================================================

export const maxDuration = 300

const HOSTGATOR_BASE = 'https://sorteiomax.com.br/omnivoice'

// ============================================================
// DEBUG LOGGER
// ============================================================

function createDebug() {
  const steps: { time: string; step: string; status: string; detail?: string; duration?: number }[] = []
  const start = Date.now()
  function log(step: string, status: 'info' | 'ok' | 'warn' | 'error', detail?: string) {
    steps.push({ time: new Date().toISOString().split('T')[1], step, status, detail: detail || '', duration: Date.now() - start })
  }
  function result() { return { totalDuration: Date.now() - start, steps } }
  return { log, result }
}

// ============================================================
// 1. TUNNEL URL (com health check)
// ============================================================

async function getTunnelUrl(debug: ReturnType<typeof createDebug>): Promise<string> {
  try {
    const res = await fetch(`${HOSTGATOR_BASE}/get_tunnel.php`, { signal: AbortSignal.timeout(10000) })
    if (!res.ok) throw new Error(`HTTP ${res.status}`)
    const data = await res.json()
    if (data.status !== 'online' || !data.tunnelUrl) {
      throw new Error(data.message || 'GPU offline')
    }

    // Health check: verificar se o tunnel esta realmente vivo
    debug.log('Tunnel URL', 'info', `Verificando: ${data.tunnelUrl.substring(0, 50)}...`)
    try {
      const healthRes = await fetch(`${data.tunnelUrl}/`, { signal: AbortSignal.timeout(8000) })
      if (!healthRes.ok) {
        debug.log('Tunnel URL', 'warn', `Tunnel esta morto (HTTP ${healthRes.status})`)
        throw new Error(`Tunnel morto (HTTP ${healthRes.status})`)
      }
      debug.log('Tunnel URL', 'ok', `${data.tunnelUrl.substring(0, 60)}... (vivo!)`)
    } catch (healthErr) {
      if (healthErr instanceof Error && healthErr.message.includes('Tunnel morto')) throw healthErr
      debug.log('Tunnel URL', 'warn', `Nao conseguiu contactar o tunnel: ${healthErr instanceof Error ? healthErr.message : String(healthErr)}`)
      throw new Error('Tunnel inacessivel - reinicie o tunnel na maquina local')
    }

    return data.tunnelUrl
  } catch (err) {
    throw new Error('GPU offline: ' + (err instanceof Error ? err.message : String(err)))
  }
}

// ============================================================
// 2. UPLOAD AUDIO PRO GRADIO (com retry)
// ============================================================

async function uploadToGradio(
  tunnelUrl: string,
  audioBuffer: ArrayBuffer,
  fileName: string,
  debug: ReturnType<typeof createDebug>
): Promise<string | null> {
  for (let attempt = 0; attempt < 3; attempt++) {
    try {
      if (attempt > 0) {
        debug.log('Upload', 'warn', `Tentativa ${attempt + 1}/3...`)
        await new Promise(r => setTimeout(r, 3000))
      }

      const blob = new Blob([audioBuffer], { type: fileName.endsWith('.mp3') ? 'audio/mpeg' : 'audio/wav' })
      const form = new FormData()
      form.append('files', blob, fileName)

      const res = await fetch(`${tunnelUrl}/gradio_api/upload`, {
        method: 'POST',
        body: form,
        signal: AbortSignal.timeout(30000),
      })

      if (!res.ok) {
        const errText = await res.text()
        debug.log('Upload', 'error', `HTTP ${res.status}: ${errText.substring(0, 200)}`)
        if (res.status >= 500) continue
        return null
      }

      const paths = await res.json()
      if (Array.isArray(paths) && paths.length > 0) {
        debug.log('Upload', 'ok', `path: ${paths[0]}`)
        return paths[0]
      }

      debug.log('Upload', 'error', 'Resposta inesperada')
      return null
    } catch (err) {
      debug.log('Upload', 'warn', `Tentativa ${attempt + 1}/3 falhou: ${err instanceof Error ? err.message : String(err)}`)
    }
  }
  debug.log('Upload', 'error', 'Falha apos 3 tentativas')
  return null
}

// ============================================================
// 3. SUBMIT JOB PRO GRADIO
// ============================================================

async function submitJob(
  tunnelUrl: string,
  data: unknown[],
  debug: ReturnType<typeof createDebug>
): Promise<string | null> {
  try {
    const res = await fetch(`${tunnelUrl}/gradio_api/call/_clone_fn`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ data }),
      signal: AbortSignal.timeout(30000),
    })

    if (!res.ok) {
      const errText = await res.text()
      debug.log('Submit', 'error', `HTTP ${res.status}: ${errText.substring(0, 200)}`)
      return null
    }

    const result = await res.json()
    const eventId = result.event_id
    debug.log('Submit', eventId ? 'ok' : 'error', eventId ? `event_id: ${eventId}` : 'sem event_id')
    return eventId
  } catch (err) {
    debug.log('Submit', 'error', err instanceof Error ? err.message : String(err))
    return null
  }
}

// ============================================================
// 4. SSE STREAM — aguardar resultado do Gradio
// ============================================================

async function streamResult(
  tunnelUrl: string,
  eventId: string,
  debug: ReturnType<typeof createDebug>,
  timeoutMs = 180000
): Promise<{ audioUrl: string | null; error: string | null }> {
  const controller = new AbortController()
  const timeoutId = setTimeout(() => controller.abort(), timeoutMs)

  try {
    const response = await fetch(
      `${tunnelUrl}/gradio_api/call/_clone_fn/${eventId}`,
      { headers: { 'Accept': 'text/event-stream' }, signal: controller.signal }
    )

    if (response.status === 404) { clearTimeout(timeoutId); return { audioUrl: null, error: '404' } }
    if (!response.ok) { clearTimeout(timeoutId); return { audioUrl: null, error: `HTTP ${response.status}` } }

    debug.log('SSE Stream', 'ok', 'Conexao aberta, aguardando resultado...')

    const reader = response.body?.getReader()
    if (!reader) { clearTimeout(timeoutId); return { audioUrl: null, error: 'No stream reader' } }

    const decoder = new TextDecoder()
    let buffer = ''
    let heartbeatCount = 0

    while (true) {
      const { done, value } = await reader.read()
      if (done) break

      buffer += decoder.decode(value, { stream: true })
      const blocks = buffer.split('\n\n')
      buffer = blocks.pop() || ''

      for (const block of blocks) {
        if (!block.trim()) continue

        const lines = block.split('\n')
        const eventLine = lines.find(l => l.startsWith('event:'))
        const dataLine = lines.find(l => l.startsWith('data:'))
        const eventType = eventLine?.replace('event: ', '').trim()
        const eventData = dataLine?.slice(6).trim()

        if (eventType === 'complete' && eventData) {
          clearTimeout(timeoutId)
          debug.log('SSE Stream', 'ok', 'Evento COMPLETE recebido!')
          try {
            const resultData = JSON.parse(eventData)
            if (!Array.isArray(resultData) || resultData.length < 2) {
              return { audioUrl: null, error: 'Formato inesperado' }
            }
            const audioOutput = resultData[0]
            let audioUrl: string | null = null
            if (audioOutput?.url) audioUrl = audioOutput.url
            else if (audioOutput?.path) audioUrl = `${tunnelUrl}/gradio_api/file=${audioOutput.path}`
            if (audioUrl) {
              debug.log('SSE Stream', 'ok', `Audio: ${audioUrl.substring(0, 80)}`)
              return { audioUrl, error: null }
            }
            return { audioUrl: null, error: 'Sem URL no output' }
          } catch { return { audioUrl: null, error: 'Parse error' } }
        }

        if (eventType === 'error') {
          clearTimeout(timeoutId)
          debug.log('SSE Stream', 'error', (eventData || 'Erro na geracao').substring(0, 200))
          return { audioUrl: null, error: eventData || 'Erro na geracao' }
        }

        if (eventType === 'heartbeat') {
          heartbeatCount++
          if (heartbeatCount <= 2 || heartbeatCount % 15 === 0) {
            debug.log('SSE Stream', 'info', `Heartbeat #${heartbeatCount}`)
          }
        }
      }
    }

    clearTimeout(timeoutId)
    return { audioUrl: null, error: 'Stream ended without result' }
  } catch (err) {
    clearTimeout(timeoutId)
    if (err instanceof Error && err.name === 'AbortError') return { audioUrl: null, error: 'timeout' }
    return { audioUrl: null, error: err instanceof Error ? err.message : String(err) }
  }
}

// ============================================================
// 5. DOWNLOAD COM RETRY + VALIDACAO WAV
// ============================================================

function isWavComplete(buf: Buffer): boolean {
  if (buf.length < 44) return false
  const declaredDataSize = buf.readUInt32LE(40)
  const actualDataSize = buf.length - 44
  return actualDataSize >= declaredDataSize
}

async function downloadWithRetry(
  url: string,
  maxRetries = 3,
  delayMs = 2000
): Promise<Buffer | null> {
  for (let attempt = 0; attempt < maxRetries; attempt++) {
    try {
      const res = await fetch(url)
      if (!res.ok) return null
      const buf = Buffer.from(await res.arrayBuffer())

      if (isWavComplete(buf)) return buf

      const declared = buf.readUInt32LE(40)
      const actual = buf.length - 44
      console.warn(`[Download] WAV truncado: header diz ${declared} bytes, recebeu ${actual} bytes. Tentativa ${attempt + 1}/${maxRetries}`)
      if (attempt < maxRetries - 1) await new Promise(r => setTimeout(r, delayMs * (attempt + 1)))
    } catch (err) {
      console.warn(`[Download] Erro na tentativa ${attempt + 1}:`, err)
      if (attempt < maxRetries - 1) await new Promise(r => setTimeout(r, delayMs))
    }
  }
  return null
}

// ============================================================
// POST HANDLER — MODO LIMPO (single-shot puro)
// ============================================================

export async function POST(req: NextRequest) {
  const debug = createDebug()

  try {
    const body = await req.json()
    const {
      referenceAudioUrl,
      referenceAudioBase64,
      referenceAudioName,
      text,
      language = 'Auto',
      instruct = '',
      speed = 1.0,
      numStep = 32,
      guidanceScale = 2.0,
      voiceMode = 'clone',
    } = body

    if (!text || !text.trim()) {
      return NextResponse.json({ error: 'Texto obrigatório', debug: debug.result() }, { status: 400 })
    }

    // Texto limpo — direto pro Gradio, sem preprocess
    const cleanText = text.trim()
    debug.log('Texto', 'info', `${cleanText.length} caracteres`)

    // 1. Descobrir tunnel
    debug.log('Tunnel', 'info', 'Descobrindo URL do tunnel...')
    const tunnelUrl = await getTunnelUrl(debug)

    // 2. Obter audio de referencia (APENAS no modo clone)
    debug.log('Voice Mode', 'info', `Modo: ${voiceMode}`)
    let audioBuffer: ArrayBuffer | null = null
    let fileName = 'reference.wav'
    let filePath: string | null = null

    if (voiceMode === 'clone') {
      debug.log('Ref Audio', 'info', 'Baixando audio de referencia...')
      if (referenceAudioBase64) {
        const base64Data = referenceAudioBase64.replace(/^data:audio\/\w+;base64,/, '')
        audioBuffer = Uint8Array.from(atob(base64Data), c => c.charCodeAt(0)).buffer
        debug.log('Ref Audio', 'ok', `Base64: ${(audioBuffer.byteLength / 1024).toFixed(1)}KB`)
      } else if (referenceAudioUrl) {
        const audioRes = await fetch(referenceAudioUrl)
        if (!audioRes.ok) throw new Error('Falha ao baixar audio de referencia')
        audioBuffer = await audioRes.arrayBuffer()
        debug.log('Ref Audio', 'ok', `Download: ${(audioBuffer.byteLength / 1024).toFixed(1)}KB`)
      } else {
        return NextResponse.json({ error: 'Audio de referencia obrigatório no modo clone', debug: debug.result() }, { status: 400 })
      }

      fileName = referenceAudioName || 'reference.wav'

      // 3. Upload pro Gradio
      debug.log('Upload', 'info', 'Enviando audio pro Gradio...')
      filePath = await uploadToGradio(tunnelUrl, audioBuffer, fileName, debug)
      if (!filePath) {
        return NextResponse.json({ error: 'Falha no upload do audio', debug: debug.result() }, { status: 502 })
      }
    } else if (voiceMode === 'design') {
      if (!instruct || !instruct.trim()) {
        return NextResponse.json({ error: 'Instruct obrigatório no modo Voice Design', debug: debug.result() }, { status: 400 })
      }
      debug.log('Voice Design', 'ok', `Instruct: "${instruct}"`)
    }

    // 4. Montar dados do Gradio — EXATAMENTE como o demo localhost
    // Ordem dos parametros do _clone_fn:
    //   [0] text, [1] language, [2] ref_audio, [3] ref_text,
    //   [4] instruct, [5] num_step, [6] guidance_scale,
    //   [7] denoise, [8] speed, [9] duration,
    //   [10] preprocess_prompt, [11] postprocess_output
    const gradioData = [
      cleanText,                          // [0] texto inteiro — SEM chunking
      language,                           // [1] idioma
      filePath ? {
        path: filePath,
        orig_name: fileName,
        mime_type: fileName.endsWith('.mp3') ? 'audio/mpeg' : 'audio/wav',
        is_stream: false,
        meta: { _type: 'gradio.FileData' },
      } : null,                           // [2] audio de referencia (null no design/auto)
      '',                                 // [3] refText: VAZIO (evita alucinacao)
      instruct || '',                     // [4] instruct
      numStep || 32,                      // [5] num_step
      guidanceScale || 2.0,              // [6] guidance_scale
      true,                               // [7] denoise
      parseFloat(speed) || 1.0,          // [8] speed — SEMPRE float
      null,                               // [9] duration: null (auto)
      true,                               // [10] preprocess_prompt
      true,                               // [11] postprocess_output
    ]

    debug.log('Parametros', 'ok', `lang:${language} speed:${parseFloat(speed) || 1.0} steps:${numStep} cfg:${guidanceScale} refText:VAZIO`)

    // 5. SINGLE-SHOT — texto inteiro de uma vez
    debug.log('Pipeline', 'ok', `MODO LIMPO SINGLE-SHOT (${cleanText.length} chars — sem efeito, sem chunking)`)

    // Submeter job
    let eventId: string | null = null
    for (let attempt = 0; attempt < 3; attempt++) {
      if (attempt > 0) {
        debug.log('Submit retry', 'warn', `Tentativa ${attempt + 1}/3`)
        await new Promise(r => setTimeout(r, 3000))
      }
      eventId = await submitJob(tunnelUrl, gradioData, debug)
      if (eventId) break
    }

    if (!eventId) {
      return NextResponse.json({ error: 'Falha ao submeter job pro Gradio', debug: debug.result() }, { status: 502 })
    }

    // SSE Stream
    const result = await streamResult(tunnelUrl, eventId, debug, 180000)
    if (!result.audioUrl) {
      return NextResponse.json({
        error: `GPU falhou ao gerar: ${result.error}`,
        debug: debug.result(),
      }, { status: 502 })
    }

    // 6. Aguardar Gradio salvar o arquivo no disco
    const delayMs = Math.min(15000, 3000 + Math.floor(cleanText.length / 100) * 500)
    await new Promise(r => setTimeout(r, delayMs))
    debug.log('Download', 'info', `Aguardou ${delayMs}ms apos SSE complete`)

    // 7. Download com retry
    const voiceBuffer = await downloadWithRetry(result.audioUrl, 3, 2000)
    if (!voiceBuffer) {
      return NextResponse.json({ error: 'Falha no download do audio', debug: debug.result() }, { status: 502 })
    }

    // 8. Verificar integridade do WAV
    if (!isWavComplete(voiceBuffer)) {
      debug.log('WAV Check', 'warn', 'WAV header incompleto mesmo apos retry — retornando mesmo assim')
    } else {
      debug.log('WAV Check', 'ok', 'WAV integro')
    }

    // 9. Info do audio gerado
    const sr = voiceBuffer.readUInt32LE(24)
    const ch = voiceBuffer.readUInt16LE(22)
    const bps = voiceBuffer.readUInt16LE(34)
    const ds = voiceBuffer.readUInt32LE(40)
    const dur = (ds / ch / Math.floor(bps / 8) / sr).toFixed(1)
    debug.log('Audio', 'ok', `${(voiceBuffer.length / 1024).toFixed(1)}KB, ${dur}s, ${sr}Hz, ${ch}ch, ${bps}bit`)

    // 10. Retornar audio — pronto, sem processamento
    debug.log('FINAL', 'ok', `Total: ${(debug.result().totalDuration / 1000).toFixed(1)}s | single-shot limpo`)

    return NextResponse.json({
      audioUrl: `data:audio/wav;base64,${voiceBuffer.toString('base64')}`,
      viaTunnel: true,
      mode: 'single-shot',
      debug: debug.result(),
    })
  } catch (error) {
    const msg = error instanceof Error ? error.message : 'Erro interno'
    debug.log('EXCEPTION', 'error', msg)
    return NextResponse.json({ error: msg, debug: debug.result() }, { status: 500 })
  }
}
