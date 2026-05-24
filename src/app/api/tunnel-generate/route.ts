import { NextRequest, NextResponse } from 'next/server'
import { chunkText, chunkByCharLimit, formatChunkSummary, type TextChunk } from '@/lib/tts-chunker'
import { type AudioChunk } from '@/lib/audio-concatenator'
import { validateGeneratedAudio, shouldRetry, formatValidationLog } from '@/lib/asr-validator'
import { stripSSMLForTTS } from '@/lib/ssml-parser'
import { trimAudioBuffer } from '@/lib/audio-trimmer'

// ============================================================
// UTIL: Validar e baixar WAV com retry
// ============================================================

/**
 * Verifica se o buffer WAV está completo (header data size == bytes reais).
 * O Cloudflare Tunnel pode truncar downloads grandes de áudio do Gradio.
 */
function isWavComplete(buf: Buffer): boolean {
  if (buf.length < 44) return false
  const declaredDataSize = buf.readUInt32LE(40)
  const actualDataSize = buf.length - 44
  return actualDataSize >= declaredDataSize
}

/**
 * Baixa audio com retry + validação WAV.
 * Se o download foi truncado (header diz que o arquivo é maior), espera e tenta de novo.
 */
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

      // Arquivo truncado — esperar e retry
      const declared = buf.readUInt32LE(40)
      const actual = buf.length - 44
      console.warn(`[Download] WAV truncado: header diz ${declared} bytes, recebeu ${actual} bytes (faltam ${declared - actual}). Tentativa ${attempt + 1}/${maxRetries}`)
      if (attempt < maxRetries - 1) await new Promise(r => setTimeout(r, delayMs * (attempt + 1)))
    } catch (err) {
      console.warn(`[Download] Erro na tentativa ${attempt + 1}:`, err)
      if (attempt < maxRetries - 1) await new Promise(r => setTimeout(r, delayMs))
    }
  }
  return null
}


// POST /api/tunnel-generate - Geracao direta via tunnel cloudflared
// Pipeline completo com prosódia:
//   1. Chunking de texto (divide por pontuação com duração de pausa)
//   2. Gera cada chunk separadamente
//   3. Concatena com silêncio real entre frases
//   4. Valida resultado com ASR (opcional)

export const maxDuration = 300

const HOSTGATOR_BASE = 'https://sorteiomax.com.br/omnivoice'

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
// FUNÇÕES AUXILIARES (tunnel, upload, submit, stream)
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
        debug.log('Tunnel URL', 'warn', `URL do PHP respondeu mas tunnel esta morto (HTTP ${healthRes.status})`)
        throw new Error(`Tunnel morto (HTTP ${healthRes.status}) - URL antiga no servidor PHP`)
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

async function uploadToGradio(
  tunnelUrl: string,
  audioBuffer: ArrayBuffer,
  fileName: string,
  debug: ReturnType<typeof createDebug>
): Promise<string | null> {
  // Tentar upload com retry (tunnel pode estar instavel)
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
        // Se for erro de conexao (tunnel morto), nao adianta retry
        if (!res.ok && res.status >= 500) continue
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

/**
 * Gera um chunk de texto via Gradio (submit + stream + download)
 */
async function generateChunk(
  tunnelUrl: string,
  chunkText: string,
  gradioBaseData: unknown[],
  debug: ReturnType<typeof createDebug>,
  chunkIndex: number,
  totalChunks: number
): Promise<Buffer | null> {
  // Substituir texto no data array
  const data = [...gradioBaseData]
  data[0] = chunkText  // índice 0 = texto
  // NAO mexer nos outros params — manter postprocess_output=true (padrao do Gradio)
  // para manter antiruido e qualidade igual ao localhost demo

  debug.log(`Chunk ${chunkIndex + 1}/${totalChunks}`, 'info', `"${chunkText.substring(0, 50)}..." (${chunkText.length} chars)`)

  // Submeter job
  let eventId: string | null = null
  for (let attempt = 0; attempt < 3; attempt++) {
    if (attempt > 0) {
      debug.log(`Chunk ${chunkIndex + 1} retry`, 'warn', `Tentativa ${attempt + 1}/3`)
      await new Promise(r => setTimeout(r, 2000))
    }
    eventId = await submitJob(tunnelUrl, data, debug)
    if (eventId) break
  }

  if (!eventId) {
    debug.log(`Chunk ${chunkIndex + 1}`, 'error', 'Falha ao submeter job')
    return null
  }

  // SSE Stream
  const result = await streamResult(tunnelUrl, eventId, debug, 180000)
  if (!result.audioUrl) {
    debug.log(`Chunk ${chunkIndex + 1}`, 'error', `Falha: ${result.error}`)
    return null
  }

  // Aguardar Gradio salvar o arquivo no disco (igual single-shot)
  // Delay curto para chunks pequenos (< 250 chars o Gradio salva rápido)
  const chunkDelay = Math.min(3000, 1500 + Math.floor(chunkText.length / 200) * 500)
  await new Promise(r => setTimeout(r, chunkDelay))

  // Download com retry
  const voiceBuffer = await downloadWithRetry(result.audioUrl, 3, 1500)
  if (!voiceBuffer) {
    debug.log(`Chunk ${chunkIndex + 1}`, 'error', 'Falha no download apos retry')
    return null
  }

  // Calcular duração do chunk para diagnóstico
  const sr = voiceBuffer.readUInt32LE(24)
  const ch = voiceBuffer.readUInt16LE(22)
  const bps = voiceBuffer.readUInt16LE(34)
  const ds = voiceBuffer.readUInt32LE(40)
  const dur = (ds / ch / Math.floor(bps / 8) / sr).toFixed(1)
  debug.log(`Chunk ${chunkIndex + 1}/${totalChunks}`, 'ok', `${(voiceBuffer.length / 1024).toFixed(1)}KB, ${dur}s, delay ${chunkDelay}ms`)

  // Retornar áudio bruto do chunk — SEM padding individual.
  // O padding no final de cada chunk causa "baixada" perceptível na junção.
  // O postprocess do OmniVoice já gera final limpo. Só padding no áudio final concatenado.
  return voiceBuffer
}

// ============================================================
// WAV HELPERS (splice cru — sem dependência do audio-concatenator)
// ============================================================

interface SimpleWavFormat {
  numChannels: number
  sampleRate: number
  byteRate: number
  blockAlign: number
  bitsPerSample: number
}

function parseWavHeaderSimple(buf: Buffer): SimpleWavFormat {
  return {
    numChannels: buf.readUInt16LE(22),
    sampleRate: buf.readUInt32LE(24),
    byteRate: buf.readUInt32LE(28),
    blockAlign: buf.readUInt16LE(32),
    bitsPerSample: buf.readUInt16LE(34),
  }
}

function buildSimpleWavHeader(fmt: SimpleWavFormat, dataSize: number, pcm: Buffer): Buffer {
  const header = Buffer.alloc(44)
  header.write('RIFF', 0)
  header.writeUInt32LE(36 + dataSize, 4)
  header.write('WAVE', 8)
  header.write('fmt ', 12)
  header.writeUInt32LE(16, 16)          // PCM
  header.writeUInt16LE(1, 20)           // PCM format
  header.writeUInt16LE(fmt.numChannels, 22)
  header.writeUInt32LE(fmt.sampleRate, 24)
  header.writeUInt32LE(fmt.byteRate, 28)
  header.writeUInt16LE(fmt.blockAlign, 32)
  header.writeUInt16LE(fmt.bitsPerSample, 34)
  header.write('data', 36)
  header.writeUInt32LE(dataSize, 40)
  return Buffer.concat([header, pcm])
}

// ============================================================
// PIPELINE COM CHUNKING — fallback only
// ============================================================

/**
 * Gera áudio com chunking (só como fallback se single-shot falhar).
 * Usa postprocess_output=true (padrão Gradio) = antiruido + qualidade.
 * Splice puro de PCM bytes — sem processamento.
 */

async function generateWithChunking(
  tunnelUrl: string,
  text: string,
  gradioBaseData: unknown[],
  debug: ReturnType<typeof createDebug>
): Promise<{ finalBuffer: Buffer; chunks: TextChunk[] } | null> {
  // 1. Chunking por limite de caracteres (anti-postprocess)
  const chunks = chunkByCharLimit(text, 250)
  if (chunks.length === 0) return null

  debug.log('Chunking', 'ok', `${chunks.length} chunks (max 250 chars cada)`)
  debug.log('Chunking', 'info', formatChunkSummary(chunks).substring(0, 500))

  // 2. Gerar cada chunk
  const audioChunks: AudioChunk[] = []
  let failedChunks = 0

  for (let i = 0; i < chunks.length; i++) {
    const chunk = chunks[i]
    const buffer = await generateChunk(tunnelUrl, chunk.text, gradioBaseData, debug, i, chunks.length)

    if (buffer) {
      audioChunks.push({ buffer, pauseAfterMs: chunk.pauseAfterMs })
    } else {
      failedChunks++
      debug.log('Chunking', 'warn', `Chunk ${i + 1} falhou, pulando (${failedChunks} falhas)`)
    }
  }

  if (audioChunks.length === 0) {
    debug.log('Chunking', 'error', 'Todos os chunks falharam')
    return null
  }

  if (failedChunks > 0) {
    debug.log('Chunking', 'warn', `${failedChunks}/${chunks.length} chunks falharam, continuando com ${audioChunks.length}`)
  }

  // 3. Raw PCM splice — sem processamento (postprocess desativado nos chunks)
  // Como postprocess_output=false, o OmniVoice NÃO corta o final do áudio.
  // Denoise=true mantém o antiruido. Splice puro de bytes.
  debug.log('Concatenacao', 'info', `Raw splice: ${audioChunks.length} chunks (postprocess OFF, denoise ON)...`)

  const firstFormat = parseWavHeaderSimple(audioChunks[0].buffer)
  const pcmParts: Buffer[] = []

  for (let i = 0; i < audioChunks.length; i++) {
    const buf = audioChunks[i].buffer
    if (buf.length < 44) continue
    const dataSize = buf.readUInt32LE(40)
    const actualDataEnd = Math.min(44 + dataSize, buf.length)
    pcmParts.push(buf.subarray(44, actualDataEnd))
  }

  const finalPcm = Buffer.concat(pcmParts)
  const finalBuffer = buildSimpleWavHeader(firstFormat, finalPcm.length, finalPcm)

  debug.log('Concatenacao', 'ok',
    `PCM: ${finalPcm.length} bytes, ${pcmParts.length} chunks, ${(finalBuffer.length / 1024).toFixed(1)}KB`)

  return { finalBuffer, chunks }
}

// ============================================================
// MODO SINGLE-SHOT (fallback sem chunking)
// ============================================================

interface AudioDiagnostics {
  textLength: number
  audioDurationSec: string
  fileSizeKB: string
  sampleRate: number
  bitsPerSample: number
  channels: number
  delayAfterSse: number
  silencePadSec: number
  expectedMinDuration: string
  durationOk: boolean
  wavHeaderValid: boolean
}

async function generateSingleShot(
  tunnelUrl: string,
  text: string,
  gradioData: unknown[],
  debug: ReturnType<typeof createDebug>
): Promise<{ buffer: Buffer | null; diagnostics: AudioDiagnostics | null }> {
  debug.log('Geracao', 'info', 'Gerando audio (single-shot, sem chunking)...')

  // Submeter job com retry
  let eventId: string | null = null
  for (let attempt = 0; attempt < 3; attempt++) {
    if (attempt > 0) {
      debug.log('Submit retry', 'warn', `Tentativa ${attempt + 1}/3`)
      await new Promise(r => setTimeout(r, 3000))
    }
    eventId = await submitJob(tunnelUrl, gradioData, debug)
    if (eventId) break
  }

  if (!eventId) return { buffer: null, diagnostics: null }

  // SSE Stream
  const result = await streamResult(tunnelUrl, eventId, debug, 180000)
  if (!result.audioUrl) return { buffer: null, diagnostics: null }

  // Aguardar Gradio terminar de escrever o arquivo no disco.
  // O evento SSE "complete" dispara quando a GERACAO termina, mas o Gradio
  // ainda pode estar salvando o arquivo WAV. Sem esse delay, o download pode
  // pegar um arquivo incompleto (cortando o final do audio em textos longos).
  // Delay dinamico: texto longo via tunnel precisa de mais tempo.
  const delayMs = Math.min(15000, 3000 + Math.floor(text.length / 100) * 500)
  await new Promise(r => setTimeout(r, delayMs))
  debug.log('Download', 'info', `Aguardou ${delayMs}ms apos SSE complete (texto: ${text.length} chars)`)

  // Download com retry + validação WAV (tunnel pode truncar arquivos grandes)
  let voiceBuffer = await downloadWithRetry(result.audioUrl, 3, 2000)
  if (!voiceBuffer) {
    debug.log('Download', 'error', 'Falha no download apos 3 tentativas')
    return { buffer: null, diagnostics: null }
  }

  // Verificar se o final do PCM tem zeros (arquivo incompleto — Gradio pré-aloca
  // o WAV com header + zeros, depois preenche o PCM. Se baixarmos antes de
  // preencher, o final fica com zeros = "fade out" no audio).
  // Checar últimos 200ms: se >80% dos samples são zero, arquivo incompleto.
  const dlSampleRate = voiceBuffer.readUInt32LE(24)
  const dlChannels = voiceBuffer.readUInt16LE(22)
  const dlBits = voiceBuffer.readUInt16LE(34)
  const dlBytesPerSample = Math.floor(dlBits / 8)
  const dlBlockAlign = dlBytesPerSample * dlChannels
  const dlDataSize = voiceBuffer.readUInt32LE(40)
  const tail200msBytes = Math.floor(dlSampleRate * 0.2) * dlBlockAlign

  if (dlDataSize > tail200msBytes) {
    const pcmStart = 44
    let zeroCount = 0
    let totalCount = 0
    for (let i = pcmStart + dlDataSize - tail200msBytes; i < pcmStart + dlDataSize; i += dlBlockAlign) {
      const sample = Math.abs(voiceBuffer.readInt16LE(i))
      totalCount++
      if (sample === 0) zeroCount++
    }
    const zeroRatio = zeroCount / totalCount
    debug.log('Download', 'info', `Tail check: ${zeroCount}/${totalCount} zeros (${(zeroRatio * 100).toFixed(0)}%) nos ultimos 200ms`)

    if (zeroRatio > 0.8) {
      debug.log('Download', 'warn', `Arquivo incompleto (${(zeroRatio * 100).toFixed(0)}% zeros no final). Aguardando +10s e baixando novamente...`)
      await new Promise(r => setTimeout(r, 10000))
      const retryBuffer = await downloadWithRetry(result.audioUrl, 3, 2000)
      if (retryBuffer) {
        // Verificar se o retry melhorou (menos zeros no final)
        let retryZeroCount = 0
        let retryTotalCount = 0
        const retryDataSize = retryBuffer.readUInt32LE(40)
        if (retryDataSize > tail200msBytes) {
          for (let i = 44 + retryDataSize - tail200msBytes; i < 44 + retryDataSize; i += dlBlockAlign) {
            const sample = Math.abs(retryBuffer.readInt16LE(i))
            retryTotalCount++
            if (sample === 0) retryZeroCount++
          }
          const retryZeroRatio = retryZeroCount / retryTotalCount
          debug.log('Download', 'info', `Retry tail: ${retryZeroCount}/${retryTotalCount} zeros (${(retryZeroRatio * 100).toFixed(0)}%)`)
          if (retryZeroRatio < zeroRatio) {
            voiceBuffer = retryBuffer
            debug.log('Download', 'ok', `Retry melhorou: ${(zeroRatio * 100).toFixed(0)}% → ${(retryZeroRatio * 100).toFixed(0)}% zeros`)
          } else {
            debug.log('Download', 'warn', `Retry nao melhorou — mantendo original`)
          }
        } else {
          voiceBuffer = retryBuffer
        }
      }
    }
  }

  // Log de duração real
  const finalDataSize = voiceBuffer.readUInt32LE(40)
  const dlDuration = (finalDataSize / dlChannels / dlBytesPerSample / dlSampleRate).toFixed(1)
  const expectedMinDuration = (text.length * 0.08).toFixed(1)
  debug.log('Download', 'ok', `Final: ${(voiceBuffer.length / 1024).toFixed(1)}KB, ${dlDuration}s (esperado >=${expectedMinDuration}s)`)

  const diagnostics: AudioDiagnostics = {
    textLength: text.length,
    audioDurationSec: dlDuration,
    fileSizeKB: (voiceBuffer.length / 1024).toFixed(1),
    sampleRate: dlSampleRate,
    bitsPerSample: dlBits,
    channels: dlChannels,
    delayAfterSse: delayMs,
    silencePadSec: 0,
    expectedMinDuration: expectedMinDuration,
    durationOk: parseFloat(dlDuration) >= parseFloat(expectedMinDuration),
    wavHeaderValid: isWavComplete(voiceBuffer),
  }
  return { buffer: voiceBuffer, diagnostics }
}

// ============================================================
// APPEND WAV SILENCE - Adiciona silêncio PCM no final de um WAV
// ============================================================

function appendWavSilence(wavBuffer: Buffer, durationSec: number): Buffer | null {
  if (wavBuffer.length < 44) return null

  // Verificar assinatura RIFF/WAVE
  const riff = wavBuffer.subarray(0, 4).toString('ascii')
  const wave = wavBuffer.subarray(8, 12).toString('ascii')
  if (riff !== 'RIFF' || wave !== 'WAVE') return null

  // Ler parâmetros do WAV header
  const sampleRate = wavBuffer.readUInt32LE(24)
  const bitsPerSample = wavBuffer.readUInt16LE(34)
  const channels = wavBuffer.readUInt16LE(22)
  const bytesPerSample = Math.floor(bitsPerSample / 8)

  // Calcular bytes de silêncio
  const silenceSamples = Math.floor(sampleRate * durationSec)
  const silenceBytes = silenceSamples * channels * bytesPerSample

  // Criar novo buffer: WAV original + zeros + header atualizado
  const newBuffer = Buffer.alloc(wavBuffer.length + silenceBytes)
  wavBuffer.copy(newBuffer)

  // Preencher silêncio (zeros) no final dos dados PCM
  newBuffer.fill(0, wavBuffer.length)

  // Atualizar RIFF ChunkSize (offset 4) = total - 8
  newBuffer.writeUInt32LE(newBuffer.length - 8, 4)

  // Atualizar Subchunk2Size (offset 40) = dados antigos + silêncio
  const oldDataSize = wavBuffer.readUInt32LE(40)
  newBuffer.writeUInt32LE(oldDataSize + silenceBytes, 40)

  return newBuffer
}

// ============================================================
// POST HANDLER
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
      refText = '',  // IGNORADO - sempre vazio para evitar alucinacao
      instruct = '',
      speed = 1.0,
      numStep = 32,
      guidanceScale = 2.0,
      skipASR = false,
      useChunking = false,  // AUTO: chunking ativa automaticamente para texto >280 chars
      voiceMode = 'clone', // 'clone' (ref_audio) | 'design' (instruct only) | 'auto' (nenhum)
    } = body

    if (!text || !text.trim()) {
      return NextResponse.json({ error: 'Texto obrigatório', debug: debug.result() }, { status: 400 })
    }

    // DEFESA DUPLA: remover tags SSML que passaram pelo frontend sem processar
    const cleanText = stripSSMLForTTS(text)
    debug.log('SSML Strip', 'info', cleanText !== text ? 'SSML detectado, tags removidas' : 'sem SSML')

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

      // Auto-trim: DESATIVADO (22/05/2026)
      // OmniVoice funciona com audio de referencia longo (24s+) sem problemas.
      // O trim brusco sem fade causava alucinacoes ("ba", "to", "sao") e audio 4x mais longo.
      // A GPU RTX 3060 12GB aguenta referencias longas com empty_cache() no omnivoice_gpu.py.
      // if (audioBuffer) {
      //   const trimResult = trimAudioBuffer(audioBuffer, fileName, 12)
      //   ...
      // }

      fileName = referenceAudioName || 'reference.wav'

      // 3. Upload pro Gradio via tunnel (UMA VEZ — referencia compartilhada entre chunks)
      debug.log('Upload', 'info', 'Enviando audio pro Gradio...')
      filePath = await uploadToGradio(tunnelUrl, audioBuffer, fileName, debug)
      if (!filePath) {
        return NextResponse.json({ error: 'Falha no upload do audio', debug: debug.result() }, { status: 502 })
      }
    } else if (voiceMode === 'design') {
      if (!instruct || !instruct.trim()) {
        return NextResponse.json({ error: 'Instruct obrigatório no modo Voice Design (ex: female, low pitch)', debug: debug.result() }, { status: 400 })
      }
      debug.log('Voice Design', 'ok', `Instruct: "${instruct}" (sem audio de referencia)`)
    } else if (voiceMode === 'auto') {
      debug.log('Auto Voice', 'ok', 'Voz automatica — modelo escolhe sozinho')
    }

    // 4. Montar dados BASE do Gradio (texto será substituído por chunk)
    // No modo design/auto, ref_audio é null (vazio)
    // LIMITAR instruct a maximo 3 palavras para evitar alucinacoes
    // Instruct longo confunde o modelo e causa fala delirada
    let safeInstruct = (instruct || '').trim()
    if (safeInstruct) {
      const parts = safeInstruct.split(/[,;\n]+/).map(s => s.trim()).filter(Boolean)
      safeInstruct = parts.slice(0, 3).join(', ')
    }

    const gradioBaseData = [
      cleanText,  // placeholder — será substituído por cada chunk
      language,
      filePath ? {
        path: filePath,
        orig_name: fileName,
        mime_type: fileName.endsWith('.mp3') ? 'audio/mpeg' : 'audio/wav',
        is_stream: false,
        meta: { _type: 'gradio.FileData' },
      } : null, // null no modo design/auto
      '',           // refText: SEMPRE VAZIO (texto aqui causa alucinacao!)
      safeInstruct, // instruct limitado a 3 partes
      numStep || 32,
      guidanceScale || 2.0,
      true,   // denoise
      speed || 1.0,
      null,   // duration
      true,   // preprocess_prompt
      true,   // postprocess_output (padrao do Gradio — funciona igual localhost:7860)
    ]

    debug.log('Parametros', 'info', `lang:${language} speed:${speed} steps:${numStep} cfg:${guidanceScale} instruct:"${safeInstruct}" refText:VAZIO chunking:${useChunking}`)

    // =============================================================
    // 5. GERAR ÁUDIO (chunking ou single-shot)
    // =============================================================
    let finalBuffer: Buffer | null = null
    let chunkInfo: TextChunk[] | null = null
    let audioDiagnostics: AudioDiagnostics | null = null

    // PIPELINE: SEMPRE tentar single-shot primeiro (igual localhost:7860)
    // O demo local funciona perfeitamente com textos longos sem chunking.
    // Chunking só como FALLBACK se single-shot falhar ou voltar audio claramente cortado.
    // Motivo de velocidade: single-shot = 1 chamada API (~30s), chunking = N chamadas (~2min)
    const useChunkingFallback = useChunking // só chunking manual se usuario pedir explicitamente
    if (!useChunkingFallback && cleanText.length > 20) {
      debug.log('Pipeline', 'info', `Modo SINGLE-SHOT (texto: ${cleanText.length} chars — igual localhost demo)`)
      const ssResult = await generateSingleShot(tunnelUrl, cleanText, gradioBaseData, debug)
      if (ssResult.buffer) {
        finalBuffer = ssResult.buffer
        audioDiagnostics = ssResult.diagnostics
      } else {
        // Single-shot falhou → fallback chunking
        debug.log('Pipeline', 'warn', 'Single-shot falhou, tentando chunking como fallback...')
        const chunkResult = await generateWithChunking(tunnelUrl, cleanText, gradioBaseData, debug)
        if (chunkResult) {
          finalBuffer = chunkResult.finalBuffer
          chunkInfo = chunkResult.chunks
          const sr = finalBuffer.readUInt32LE(24)
          const ch = finalBuffer.readUInt16LE(22)
          const bps = finalBuffer.readUInt16LE(34)
          const ds = finalBuffer.readUInt32LE(40)
          const dur = (ds / ch / Math.floor(bps / 8) / sr).toFixed(1)
          const expDur = (cleanText.length * 0.08).toFixed(1)
          audioDiagnostics = {
            textLength: cleanText.length, audioDurationSec: dur,
            fileSizeKB: (finalBuffer.length / 1024).toFixed(1),
            sampleRate: sr, bitsPerSample: bps, channels: ch,
            delayAfterSse: 0, silencePadSec: 0,
            expectedMinDuration: expDur,
            durationOk: parseFloat(dur) >= parseFloat(expDur),
            wavHeaderValid: isWavComplete(finalBuffer),
          }
        }
      }
    } else if (useChunkingFallback) {
      // Chunking manual (usuario pediu explicitamente)
      debug.log('Pipeline', 'info', `Modo CHUNKING manual (texto: ${cleanText.length} chars)`)
      const chunkResult = await generateWithChunking(tunnelUrl, cleanText, gradioBaseData, debug)
      if (chunkResult) {
        finalBuffer = chunkResult.finalBuffer
        chunkInfo = chunkResult.chunks
      }
    }

    // 6. Verificar resultado
    if (!finalBuffer) {
      return NextResponse.json({
        error: 'GPU nao conseguiu gerar audio',
        debug: debug.result(),
      }, { status: 500 })
    }

    // 7. Validação ASR (opcional, no audio final)
    let asrResult = null
    if (!skipASR && finalBuffer) {
      debug.log('ASR', 'info', 'Validando audio final com ASR...')
      asrResult = await validateGeneratedAudio(
        new Uint8Array(finalBuffer).buffer as ArrayBuffer,
        text
      )
      debug.log('ASR', asrResult.valid ? 'ok' : 'warn', formatValidationLog(asrResult))
    }

    // 8. Montar resposta
    const voiceDataUri = `data:audio/wav;base64,${finalBuffer.toString('base64')}`

    const response: Record<string, unknown> = {
      audioUrl: voiceDataUri,
      viaTunnel: true,
      mode: chunkInfo ? 'chunking' : 'single-shot',
      debug: debug.result(),
      audioDiagnostics,
    }

    // Info do chunking
    if (chunkInfo) {
      response.chunking = {
        totalChunks: chunkInfo.length,
        chunks: chunkInfo.map(c => ({
          text: c.text.substring(0, 50),
          pauseAfterMs: c.pauseAfterMs,
          punctuation: c.punctuation,
        })),
      }
    }

    // Info do ASR
    if (asrResult) {
      response.asrValidation = {
        valid: asrResult.valid,
        method: asrResult.method,
        transcription: asrResult.transcription,
        confidence: Math.round(asrResult.confidence * 100),
        wordCoverage: asrResult.wordCoverage >= 0 ? Math.round(asrResult.wordCoverage * 100) : 'N/A',
        issues: asrResult.issues,
      }
      if (!asrResult.valid) {
        response.asrWarning = true
        response.asrMessage = 'Audio pode conter imperfeicoes.'
      }
    }

    debug.log('FINAL', 'ok', `Total: ${(debug.result().totalDuration / 1000).toFixed(1)}s | modo: ${chunkInfo ? 'chunking' : 'single-shot'}`)

    return NextResponse.json(response)
  } catch (error) {
    const msg = error instanceof Error ? error.message : 'Erro interno'
    debug.log('EXCEPTION', 'error', msg)
    return NextResponse.json({ error: msg, debug: debug.result() }, { status: 500 })
  }
}
