/**
 * ASR Validator — Validação inteligente de áudio gerado por TTS
 * 
 * Camada 2 do sistema de qualidade OmniVoice:
 * 1. Prevenção: refText vazio, denoise on, preprocess on (ja implementado)
 * 2. Correção: ASR + filtro inteligente + retry (ESTE MODULO)
 * 3. Fallback: marcar voz como problemática se falhar várias vezes
 * 
 * Não altera a geração — só valida o resultado e pede regeneração se necessário.
 * Se ASR falhar/timar, retorna o áudio original (graceful degradation).
 */

import ZAI from 'z-ai-web-dev-sdk'

// ============================================================
// CONFIGURAÇÃO
// ============================================================

const ASR_TIMEOUT_MS = 15000        // timeout do ASR (15s)
const MAX_RETRY_ATTEMPTS = 3        // max regenerações por validação
const WORD_COVERAGE_MIN = 0.70       // min 70% das palavras originais devem aparecer
const EXTRA_WORDS_MAX_RATIO = 0.25   // max 25% de palavras extras aceitáveis
const MIN_ORIGINAL_WORDS = 3         // ignora validação se texto tem menos de 3 palavras

// Palavras lixo que o TTS às vezes alucina (PT-BR)
const JUNK_WORDS = [
  'to', 'tô', 'ta', 'tá',
  'ba', 'bah',
  'ahn', 'ah', 'ahn',
  'eh', 'éh', 'êh',
  'hum', 'hmm', 'hm',
  'oh', 'ô',
  'ih', 'íh',
  'uh', 'úh',
  'ai', 'áí',
  'psiu', 'ps',
]

// ============================================================
// TIPOS
// ============================================================

export interface ValidationResult {
  valid: boolean
  transcription: string
  confidence: number        // 0-1, quanto mais perto de 1, mais confiável
  issues: string[]          // lista de problemas detectados
  wordCoverage: number      // % das palavras originais que apareceram
  extraWordsRatio: number   // % de palavras extras na transcrição
}

interface ValidationConfig {
  enabled: boolean          // master switch
  maxRetries: number        // max tentativas de regeneração
  skipShortTexts: boolean   // pula validação para textos muito curtos
}

// ============================================================
// SDK INSTANCE (singleton)
// ============================================================

let zaiInstance: InstanceType<typeof ZAI> | null = null

async function getZAI(): Promise<InstanceType<typeof ZAI>> {
  if (!zaiInstance) {
    zaiInstance = await ZAI.create()
  }
  return zaiInstance
}

// ============================================================
// FUNÇÕES DE NORMALIZAÇÃO
// ============================================================

/** Normaliza texto para comparação: lowercase, sem pontuação, sem acentos */
function normalizeText(text: string): string {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')  // remove acentos
    .replace(/[^\w\s]/g, '')          // remove pontuação
    .replace(/\s+/g, ' ')
    .trim()
}

/** Converte texto em array de palavras úteis (sem stopwords comuns) */
function extractWords(text: string): string[] {
  const normalized = normalizeText(text)
  return normalized.split(' ').filter(w => w.length > 0)
}

/** Remove palavras lixo de um array de palavras */
function removeJunkWords(words: string[]): string[] {
  return words.filter(w => !JUNK_WORDS.includes(w.toLowerCase()))
}

// ============================================================
// COMPARAÇÃO INTELIGENTE
// ============================================================

/**
 * Compara a transcrição ASR com o texto original
 * Retorna detalhes sobre o que divergiu
 */
function compareTexts(originalText: string, transcription: string): {
  wordCoverage: number
  extraWordsRatio: number
  issues: string[]
  junkAtStart: boolean
  junkWords: string[]
} {
  const originalWords = extractWords(originalText)
  const transcribedWords = extractWords(transcription)

  // Remove palavras lixo da transcrição para análise
  const transcribedClean = removeJunkWords(transcribedWords)
  const junkWordsFound = transcribedWords.filter(w => JUNK_WORDS.includes(w.toLowerCase()))
  
  // Verifica se há palavras lixo no INÍCIO da transcrição (sinal forte de alucinação)
  let junkAtStart = false
  if (transcribedWords.length > 0 && JUNK_WORDS.includes(transcribedWords[0].toLowerCase())) {
    junkAtStart = true
  }
  // Também verifica se os 2 primeiros são junk
  if (transcribedWords.length > 1 && JUNK_WORDS.includes(transcribedWords[1].toLowerCase())) {
    junkAtStart = true
  }

  const issues: string[] = []

  // Texto muito curto — skip
  if (originalWords.length < MIN_ORIGINAL_WORDS) {
    return { wordCoverage: 1, extraWordsRatio: 0, issues: [], junkAtStart: false, junkWords: [] }
  }

  // 1. Detectar palavras lixo no início
  if (junkAtStart) {
    issues.push(`Palavras lixo no inicio: "${junkWordsFound.slice(0, 3).join(', ')}"`)
  }

  // 2. Calcular cobertura: quantas palavras do original apareceram na transcrição
  const transcribedSet = new Set(transcribedClean.map(w => w.toLowerCase()))
  const matchedWords = originalWords.filter(w => transcribedSet.has(w.toLowerCase()))
  const wordCoverage = originalWords.length > 0
    ? matchedWords.length / originalWords.length
    : 0

  if (wordCoverage < WORD_COVERAGE_MIN) {
    issues.push(`Cobertura baixa: ${(wordCoverage * 100).toFixed(0)}% (${matchedWords.length}/${originalWords.length} palavras)`)
  }

  // 3. Calcular palavras extras (na transcrição mas não no original)
  const originalSet = new Set(originalWords.map(w => w.toLowerCase()))
  const extraWords = transcribedClean.filter(w => !originalSet.has(w.toLowerCase()) && !JUNK_WORDS.includes(w.toLowerCase()))
  const extraWordsRatio = transcribedClean.length > 0
    ? extraWords.length / transcribedClean.length
    : 0

  if (extraWordsRatio > EXTRA_WORDS_MAX_RATIO) {
    issues.push(`Muitas palavras extras: ${extraWords.length} palavras nao estao no texto original`)
  }

  // 4. Verificar se a transcrição está em idioma muito diferente
  // Se a cobertura é muito baixa (< 30%), provavelmente gerou em outro idioma
  if (wordCoverage < 0.30) {
    issues.push('Possivel geracao em outro idioma')
  }

  return { wordCoverage, extraWordsRatio, issues, junkAtStart, junkWords: junkWordsFound }
}

// ============================================================
// ASR TRANSCRIPTION
// ============================================================

/**
 * Transcreve audio usando ASR (z-ai-web-dev-sdk)
 * Retorna texto transcrito ou null se falhar
 */
async function transcribeAudio(audioBuffer: ArrayBuffer): Promise<string | null> {
  try {
    const zai = await getZAI()
    const base64Audio = Buffer.from(audioBuffer).toString('base64')

    const response = await zai.audio.asr.create({
      file_base64: base64Audio,
    })

    if (!response?.text || response.text.trim().length === 0) {
      console.log('[ASR Validator] Transcrição vazia')
      return null
    }

    console.log('[ASR Validator] Transcrição:', response.text)
    return response.text
  } catch (err) {
    console.error('[ASR Validator] Falha na transcrição:', err instanceof Error ? err.message : String(err))
    return null
  }
}

// ============================================================
// VALIDAÇÃO PRINCIPAL
// ============================================================

/**
 * Valida áudio gerado comparando com o texto original via ASR
 * 
 * Retorna ValidationResult com detalhes. Nunca joga erro — se ASR falhar,
 * retorna valid=true (graceful degradation).
 */
export async function validateGeneratedAudio(
  audioBuffer: ArrayBuffer,
  originalText: string,
  config: Partial<ValidationConfig> = {}
): Promise<ValidationResult> {
  const cfg: ValidationConfig = {
    enabled: config.enabled ?? true,
    maxRetries: config.maxRetries ?? MAX_RETRY_ATTEMPTS,
    skipShortTexts: config.skipShortTexts ?? true,
  }

  // Fallback: texto muito curto, aceita direto
  const words = extractWords(originalText)
  if (cfg.skipShortTexts && words.length < MIN_ORIGINAL_WORDS) {
    return {
      valid: true,
      transcription: '(texto curto, validação pulada)',
      confidence: 1,
      issues: [],
      wordCoverage: 1,
      extraWordsRatio: 0,
    }
  }

  // Tentar transcrever com timeout
  console.log('[ASR Validator] Iniciando validação ASR...')
  const transcription = await Promise.race([
    transcribeAudio(audioBuffer),
    new Promise<null>(resolve => setTimeout(() => {
      console.log('[ASR Validator] Timeout — pulando validação')
      resolve(null)
    }, ASR_TIMEOUT_MS)),
  ])

  // Se ASR falhou, retorna válido (graceful degradation)
  if (!transcription) {
    return {
      valid: true,
      transcription: '(ASR indisponível, validação pulada)',
      confidence: 0.5,
      issues: ['ASR indisponível — validação pulada (graceful degradation)'],
      wordCoverage: 1,
      extraWordsRatio: 0,
    }
  }

  // Comparar textos
  const comparison = compareTexts(originalText, transcription)

  // Decisão: válido ou não?
  const isValid = !comparison.junkAtStart
    && comparison.wordCoverage >= WORD_COVERAGE_MIN
    && comparison.extraWordsRatio <= EXTRA_WORDS_MAX_RATIO
    && !comparison.issues.some(i => i.includes('outro idioma'))

  // Calcular confiança geral
  const confidence = Math.min(
    comparison.wordCoverage,
    1 - comparison.extraWordsRatio,
    comparison.junkAtStart ? 0.3 : 1
  )

  return {
    valid: isValid,
    transcription,
    confidence,
    issues: comparison.issues,
    wordCoverage: comparison.wordCoverage,
    extraWordsRatio: comparison.extraWordsRatio,
  }
}

// ============================================================
// HELPERS
// ============================================================

/** Verifica se é necessário tentar regenerar com base na validação */
export function shouldRetry(validation: ValidationResult): boolean {
  // Se tá válido, não precisa
  if (validation.valid) return false
  // Se o ASR nem funcionou (graceful degradation), não perde tempo
  if (validation.confidence === 0.5) return false
  // Se tem issues graves, sim, vale a pena tentar
  return validation.issues.length > 0
}

/** Formata resultado da validação para log */
export function formatValidationLog(validation: ValidationResult): string {
  if (validation.valid) {
    return `[ASR] VALIDO — cobertura: ${(validation.wordCoverage * 100).toFixed(0)}%, confiança: ${(validation.confidence * 100).toFixed(0)}%`
  }
  return `[ASR] REJEITADO — ${validation.issues.join('; ')} | transcricao: "${validation.transcription.substring(0, 80)}"`
}
