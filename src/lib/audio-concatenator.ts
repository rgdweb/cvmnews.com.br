/**
 * Audio Concatenator — Concatena áudios WAV com qualidade profissional
 * 
 * Pipeline de pós-processamento:
 * 1. Trim de silêncio (corta silêncio morto do início/fim de cada chunk)
 * 2. Normalização de volume (RMS — todas frases no mesmo nível)
 * 3. Crossfade entre chunks (50ms — transição suave)
 * 4. Silêncio real entre frases (pausas em ms)
 * 5. Fade-out final (200ms — sem corte abrupto)
 * 
 * Funciona com WAV PCM 16-bit (mono/estéreo).
 */

// ============================================================
// TIPOS
// ============================================================

export interface AudioChunk {
  buffer: Buffer
  pauseAfterMs: number
}

export interface ConcatenationResult {
  buffer: Buffer
  format: 'wav'
  totalDurationMs: number
  chunkCount: number
  chunksInfo: { index: number; durationMs: number; pauseAfterMs: number }[]
}

export interface ConcatenationConfig {
  crossfadeMs: number      // crossfade entre chunks (0 = sem crossfade)
  trimSilenceMs: number    // trim de silêncio no início/fim de cada chunk
  normalizeVolume: boolean // normaliza RMS entre chunks
  fadeOutMs: number        // fade-out final
  targetRmsDb: number      // volume alvo para normalização (-16 dB)
}

const DEFAULT_CONFIG: ConcatenationConfig = {
  crossfadeMs: 50,
  trimSilenceMs: 80,       // 80ms de silêncio morto pra cortar
  normalizeVolume: true,
  fadeOutMs: 200,
  targetRmsDb: -16,
}

// ============================================================
// WAV HELPERS
// ============================================================

interface WavFormat {
  numChannels: number
  sampleRate: number
  bitsPerSample: number
  byteRate: number
  blockAlign: number
  dataSize: number
}

function isWav(buffer: Buffer): boolean {
  if (buffer.length < 44) return false
  return buffer.toString('ascii', 0, 4) === 'RIFF' &&
         buffer.toString('ascii', 8, 12) === 'WAVE'
}

function parseWavHeader(buffer: Buffer): WavFormat | null {
  if (!isWav(buffer)) return null
  return {
    numChannels: buffer.readUInt16LE(22),
    sampleRate: buffer.readUInt32LE(24),
    byteRate: buffer.readUInt32LE(28),
    blockAlign: buffer.readUInt16LE(32),
    bitsPerSample: buffer.readUInt16LE(34),
    dataSize: buffer.readUInt32LE(40),
  }
}

function buildWavHeader(format: WavFormat, dataSize: number): Buffer {
  const header = Buffer.alloc(44)
  header.write('RIFF', 0)
  header.writeUInt32LE(36 + dataSize, 4)
  header.write('WAVE', 8)
  header.write('fmt ', 12)
  header.writeUInt32LE(16, 16) // chunk size
  header.writeUInt16LE(1, 20)  // PCM
  header.writeUInt16LE(format.numChannels, 22)
  header.writeUInt32LE(format.sampleRate, 24)
  header.writeUInt32LE(format.byteRate, 28)
  header.writeUInt16LE(format.blockAlign, 32)
  header.writeUInt16LE(format.bitsPerSample, 34)
  header.write('data', 36)
  header.writeUInt32LE(dataSize, 40)
  return header
}

function bytesPerMs(format: WavFormat): number {
  return (format.sampleRate * format.blockAlign) / 1000
}

function msToBytes(ms: number, format: WavFormat): number {
  return Math.round(ms * bytesPerMs(format))
}

/** Lê sample 16-bit em posição absoluta (incluindo header offset) */
function readSample16(buf: Buffer, pos: number): number {
  return buf.readInt16LE(pos)
}

function writeSample16(buf: Buffer, pos: number, value: number): void {
  buf.writeInt16LE(Math.max(-32768, Math.min(32767, Math.round(value))), pos)
}

// ============================================================
// TRIM DE SILÊNCIO
// ============================================================

/**
 * Corta silêncio morto do início e fim do áudio.
 * Silêncio = samples abaixo de um threshold (muito baixos).
 */
export function trimSilence(wavBuffer: Buffer, trimMs: number): Buffer {
  const format = parseWavHeader(wavBuffer)
  if (!format || format.bitsPerSample !== 16) return wavBuffer

  const threshold = 200 // samples abaixo de ~200 são silêncio
  const maxTrimBytes = msToBytes(trimMs, format)
  const dataStart = 44
  const dataEnd = 44 + format.dataSize

  // Encontrar início real (primeiro sample acima do threshold)
  let startByte = dataStart
  const startLimit = Math.min(dataStart + maxTrimBytes, dataEnd)
  for (let i = dataStart; i < startLimit; i += format.blockAlign) {
    const sample = Math.abs(readSample16(wavBuffer, i))
    if (sample > threshold) {
      startByte = i
      break
    }
  }

  // Encontrar fim real (último sample acima do threshold)
  let endByte = dataEnd
  const endLimit = Math.max(dataEnd - maxTrimBytes, dataStart)
  for (let i = dataEnd - format.blockAlign; i >= endLimit; i -= format.blockAlign) {
    const sample = Math.abs(readSample16(wavBuffer, i))
    if (sample > threshold) {
      endByte = i + format.blockAlign
      break
    }
  }

  const trimmedSize = endByte - startByte
  if (trimmedSize <= 0 || trimmedSize >= format.dataSize) return wavBuffer

  // Construir novo WAV com dados aparados
  const newDataSize = trimmedSize
  const output = Buffer.concat([
    buildWavHeader(format, newDataSize),
    wavBuffer.subarray(startByte, endByte),
  ])

  return output
}

// ============================================================
// NORMALIZAÇÃO DE VOLUME (RMS)
// ============================================================

/**
 * Calcula o RMS (Root Mean Square) de um buffer WAV em dB.
 */
function calculateRmsDb(wavBuffer: Buffer): number {
  const format = parseWavHeader(wavBuffer)
  if (!format || format.bitsPerSample !== 16) return 0

  const dataStart = 44
  const dataEnd = 44 + format.dataSize
  let sumSquares = 0
  let count = 0

  for (let i = dataStart; i < dataEnd; i += format.blockAlign) {
    const sample = readSample16(wavBuffer, i)
    sumSquares += sample * sample
    count++
  }

  if (count === 0) return -Infinity
  const rms = Math.sqrt(sumSquares / count)
  return 20 * Math.log10(rms / 32768)
}

/**
 * Normaliza o volume de um áudio para um RMS alvo.
 * Aplica ganho linear sem clipar.
 */
export function normalizeVolume(wavBuffer: Buffer, targetRmsDb: number = -16): Buffer {
  const format = parseWavHeader(wavBuffer)
  if (!format || format.bitsPerSample !== 16) return wavBuffer

  const currentRmsDb = calculateRmsDb(wavBuffer)
  if (!isFinite(currentRmsDb)) return wavBuffer

  const gainDb = targetRmsDb - currentRmsDb
  const gainLinear = Math.pow(10, gainDb / 20)

  // Limitar ganho para não distorcer
  const clampedGain = Math.min(gainLinear, 2.0) // max 2x

  if (Math.abs(clampedGain - 1.0) < 0.05) return wavBuffer // diferença insignificante

  const output = Buffer.from(wavBuffer)
  const dataStart = 44
  const dataEnd = 44 + format.dataSize

  for (let i = dataStart; i < dataEnd; i += 2) {
    const sample = readSample16(output, i)
    writeSample16(output, i, sample * clampedGain)
  }

  return output
}

// ============================================================
// CROSSFADE ENTRE CHUNKS
// ============================================================

/**
 * Aplica crossfade entre o final de chunk A e o início de chunk B.
 * Durante a região de overlap, o volume de A diminui e o de B aumenta.
 * 
 * A: [...audio...][fade-out region]
 * B: [fade-in region][...audio...]
 * Resultado: [...audio...][crossfade mix][...audio...]
 */
function crossfadeBuffers(
  bufferA: Buffer, bufferB: Buffer,
  format: WavFormat,
  crossfadeMs: number
): Buffer {
  const crossfadeBytes = msToBytes(crossfadeMs, format)
  const is16bit = format.bitsPerSample === 16
  if (!is16bit || crossfadeBytes < format.blockAlign) {
    // Sem crossfade suficiente, concatenação simples
    return Buffer.concat([bufferA, bufferB])
  }

  // Dados A (completo)
  const dataAStart = 44
  const dataAEnd = 44 + parseWavHeader(bufferA)!.dataSize

  // Dados B (completo)
  const dataBStart = 44
  const dataBEnd = 44 + parseWavHeader(bufferB)!.dataSize

  // Limitar crossfade ao menor dos dois lados
  const maxFadeA = dataAEnd - dataAStart
  const maxFadeB = dataBEnd - dataBStart
  const actualFadeBytes = Math.min(crossfadeBytes, maxFadeA, maxFadeB)

  if (actualFadeBytes < format.blockAlign) {
    return Buffer.concat([bufferA, bufferB])
  }

  // Montar output:
  // [header][dados A sem final][crossfade][dados B sem início]
  const partASize = dataAEnd - dataAStart - actualFadeBytes
  const partBSize = dataBEnd - dataBStart - actualFadeBytes
  const totalDataSize = partASize + actualFadeBytes + partBSize

  const output = Buffer.concat([
    buildWavHeader(format, totalDataSize),
    bufferA.subarray(dataAStart, dataAStart + partASize),  // A sem final
  ])

  // Crossfade region — mix de A (fade-out) e B (fade-in)
  const fadeStartA = dataAEnd - actualFadeBytes
  const fadeStartB = dataBStart

  for (let i = 0; i < actualFadeBytes; i += format.blockAlign) {
    const progress = i / actualFadeBytes // 0 a 1
    const gainA = 1 - progress  // fade-out
    const gainB = progress       // fade-in

    for (let ch = 0; ch < format.numChannels; ch++) {
      const sampleA = readSample16(bufferA, fadeStartA + i + ch * 2)
      const sampleB = readSample16(bufferB, fadeStartB + i + ch * 2)
      const mixed = Math.round(sampleA * gainA + sampleB * gainB)
      writeSample16(output, 44 + partASize + i + ch * 2, mixed)
    }
  }

  // Adicionar resto de B
  const restB = bufferB.subarray(fadeStartB + actualFadeBytes, dataBEnd)
  return Buffer.concat([output, restB])
}

// ============================================================
// FADE-OUT FINAL
// ============================================================

export function applyFadeOut(wavBuffer: Buffer, fadeOutMs: number): Buffer {
  const format = parseWavHeader(wavBuffer)
  if (!format || format.bitsPerSample !== 16) return wavBuffer

  const fadeOutBytes = Math.min(msToBytes(fadeOutMs, format), format.dataSize)
  if (fadeOutBytes <= 0) return wavBuffer

  const output = Buffer.from(wavBuffer)
  const fadeStart = 44 + format.dataSize - fadeOutBytes

  for (let i = fadeStart; i < 44 + format.dataSize; i += 2) {
    const progress = (i - fadeStart) / fadeOutBytes
    const factor = 1 - progress
    const sample = readSample16(output, i)
    writeSample16(output, i, sample * factor)
  }

  return output
}

// ============================================================
// CONCATENAÇÃO PRINCIPAL (com todo o pipeline)
// ============================================================

/**
 * Concatena múltiplos chunks com qualidade profissional:
 * 1. Trim de silêncio
 * 2. Normalização de volume
 * 3. Crossfade entre chunks
 * 4. Silêncio real entre frases
 * 5. Fade-out final
 */
export function concatenateAudioBuffers(
  chunks: AudioChunk[],
  config: Partial<ConcatenationConfig> = {}
): ConcatenationResult {
  const cfg = { ...DEFAULT_CONFIG, ...config }

  if (chunks.length === 0) {
    throw new Error('Nenhum chunk de áudio para concatenar')
  }

  // Se só tem 1 chunk, aplicar trim + fade-out
  if (chunks.length === 1) {
    let buffer = chunks[0].buffer
    if (cfg.trimSilenceMs > 0) buffer = trimSilence(buffer, cfg.trimSilenceMs)
    if (cfg.normalizeVolume) buffer = normalizeVolume(buffer, cfg.targetRmsDb)
    if (cfg.fadeOutMs > 0) buffer = applyFadeOut(buffer, cfg.fadeOutMs)

    const format = parseWavHeader(buffer)!
    return {
      buffer,
      format: 'wav',
      totalDurationMs: Math.round(format.dataSize / bytesPerMs(format)),
      chunkCount: 1,
      chunksInfo: [{ index: 0, durationMs: Math.round(format.dataSize / bytesPerMs(format)), pauseAfterMs: 0 }],
    }
  }

  // Verificar se todos são WAV
  if (!chunks.every(c => isWav(c.buffer))) {
    throw new Error('Todos os chunks devem ser WAV para concatenacao.')
  }

  const format = parseWavHeader(chunks[0].buffer)!
  const chunksInfo: ConcatenationResult['chunksInfo'] = []

  // Passo 1: Pré-processar cada chunk (trim + normalize)
  const processedChunks = chunks.map((chunk, i) => {
    let buf = chunk.buffer

    // Trim silêncio
    if (cfg.trimSilenceMs > 0) {
      buf = trimSilence(buf, cfg.trimSilenceMs)
    }

    // Normalizar volume
    if (cfg.normalizeVolume) {
      buf = normalizeVolume(buf, cfg.targetRmsDb)
    }

    const fmt = parseWavHeader(buf)!
    const durationMs = Math.round(fmt.dataSize / bytesPerMs(fmt))
    chunksInfo.push({ index: i, durationMs, pauseAfterMs: chunk.pauseAfterMs })

    return { buffer: buf, durationMs }
  })

  // Passo 2: Concatenar com crossfade + silêncio
  let currentBuffer = processedChunks[0].buffer

  for (let i = 1; i < processedChunks.length; i++) {
    const prevPauseMs = chunks[i - 1].pauseAfterMs

    if (prevPauseMs > 0) {
      // Inserir silêncio entre chunks
      const silenceBytes = msToBytes(prevPauseMs, format)
      const silence = Buffer.alloc(silenceBytes)
      const silenceWav = Buffer.concat([buildWavHeader(format, silenceBytes), silence])
      currentBuffer = crossfadeBuffers(currentBuffer, silenceWav, format, 0)
    }

    // Crossfade + concatenar próximo chunk
    currentBuffer = crossfadeBuffers(currentBuffer, processedChunks[i].buffer, format, cfg.crossfadeMs)
  }

  // Passo 3: Fade-out final
  if (cfg.fadeOutMs > 0) {
    currentBuffer = applyFadeOut(currentBuffer, cfg.fadeOutMs)
  }

  const finalFormat = parseWavHeader(currentBuffer)!
  const totalDurationMs = Math.round(finalFormat.dataSize / bytesPerMs(finalFormat))

  return {
    buffer: currentBuffer,
    format: 'wav',
    totalDurationMs,
    chunkCount: chunks.length,
    chunksInfo,
  }
}

/** Estima duração do WAV em ms */
function estimateWavDuration(buffer: Buffer): number {
  const format = parseWavHeader(buffer)
  if (!format) return 0
  return Math.round(format.dataSize / bytesPerMs(format))
}
