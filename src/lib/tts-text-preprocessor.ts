/**
 * TTS Text Preprocessor — Melhora a interpretação de pontuação pelo F5-TTS
 * 
 * O F5-TTS tende a:
 * - Ignorar pontos/vírgulas (fala tudo junto)
 * - Cortar a última palavra da frase (não finaliza direito)
 * - Não fazer pausas entre frases
 * 
 * ATENÇÃO: O modelo LÊ os caracteres literais. Não adicionar "..." porque
 * ele vai TENTAR FALAR "ponto ponto ponto". Em vez disso, usar:
 * - Newlines (\n) entre frases = quebra de sentença natural do modelo
 * - Espaços extras ao redor de pontuação = micro-pausa
 * - Repetir última palavra levemente no final = garante que finalize
 */

// ============================================================
// CONFIGURAÇÃO
// ============================================================

interface PreprocessConfig {
  enabled: boolean
  useNewlines: boolean     // quebra de linha entre frases (pause natural)
  commaSpace: boolean      // espaço extra depois de vírgula
  repeatLastWord: boolean  // repete última palavra com pontuação (evita corte)
  sentenceBreak: boolean   // quebra frases muito longas
  maxSentenceLength: number
  autoSpeed: boolean       // ajusta velocidade automaticamente baseado no texto
}

const DEFAULT_CONFIG: PreprocessConfig = {
  enabled: true,
  useNewlines: true,
  commaSpace: true,
  repeatLastWord: false,   // desativado — soa estranho repetir palavras
  sentenceBreak: true,
  maxSentenceLength: 20,
  autoSpeed: true,         // ATIVADO — reduz velocidade quando texto é complexo
}

// ============================================================
// PRÉ-PROCESSAMENTO PRINCIPAL
// ============================================================

/**
 * Pré-processa texto para TTS
 * 
 * Transformações:
 * - ". ! ?" → ".\n" (newline = quebra de sentença forte)
 * - "," → ", " (espaço extra = micro-pausa)
 * - ";" ":" → ".\n" (quebra de sentença média)
 * - Frases longas → quebra com newline
 * - NÃO adiciona caracteres faláveis (!!!)
 */
export function preprocessTTS(text: string, config: Partial<PreprocessConfig> = {}): string {
  const cfg = { ...DEFAULT_CONFIG, ...config }

  if (!cfg.enabled) return text

  let result = text

  // 0. Reticências Unicode (U+2026 …) → remover (ANTES de qualquer regex de ponto)
  result = result.replace(/\u2026/g, ' ')

  // 1. Pontuação dupla/errada: "!." "?." ".." "!!" → manter só a primeira
  result = result.replace(/([!?])[.]+/g, '$1')
  result = result.replace(/([.])[.]+/g, '$1')
  result = result.replace(/([!?])[!?]+/g, '$1')

  // 2. "..." ou ".." no FINAL de palavras = pausa dramática (emoção!)
  // Converte para newline + newline = pausa longa sem falar os pontos
  result = result.replace(/\.{2,}\s*/g, '\n\n')

  // 3. "!" no final = pausa com energia (exclamação vira quebra forte)
  // "!" no meio da frase = manter (o TTS usa pra ênfase sem falar)
  result = result.replace(/!+/g, (match, offset, str) => {
    const after = str.substring(offset + match.length).trim()
    // Se tem texto depois, manter o ! (ênfase no meio da frase)
    if (after.length > 0 && !/^[.!?,;:\n]/.test(after)) return match
    // Se é final de frase, virar newline (pausa energética)
    return '\n'
  })

  // 4. "?" no final = pausa com tom de pergunta
  result = result.replace(/\?+/g, (match, offset, str) => {
    const after = str.substring(offset + match.length).trim()
    if (after.length > 0 && !/^[.!?,;:\n]/.test(after)) return match
    return '\n'
  })

  // 5. "." final de frase = pausa normal (newline)
  if (cfg.useNewlines) {
    result = result.replace(/([.])\s*/g, '$1\n')
  }

  // 6. Vírgula → espaço extra (micro-pausa, o TTS respeita sem falar)
  if (cfg.commaSpace) {
    result = result.replace(/,\s*/g, ',  ')
  }

  // 7. Ponto e vírgula / dois pontos → newline
  result = result.replace(/[;:]\s*/g, '\n')

  // 8. Limpar newlines múltiplos (mas manter double-newline como pausa dramática)
  result = result.replace(/\n{3,}/g, '\n\n')

  // 9. Limpar espaços múltiplos
  result = result.split('\n').map(line => line.trim().replace(/  +/g, ' ')).join('\n')

  // 10. Frases muito longas → quebrar com newline
  if (cfg.sentenceBreak) {
    result = breakLongSentences(result, cfg.maxSentenceLength)
  }

  // 11. Repetir última palavra de cada frase (opcional)
  if (cfg.repeatLastWord) {
    result = repeatLastWordOfSentences(result)
  }

  // 12. Limpar linhas vazias (MAS manter double-newline como pausa dramática)
  const lines = result.split('\n')
  const finalLines: string[] = []
  for (const line of lines) {
    const trimmed = line.trim()
    if (!trimmed) {
      // Linha vazia = pausa dramática (vem de "..." ou "!.")
      // Adicionar como linha especial que o TTS interpreta como silêncio
      if (finalLines.length > 0 && finalLines[finalLines.length - 1] !== '') {
        finalLines.push('...')
      }
      continue
    }
    // Remover pontuação solta no final: "estremecer." → "estremecer"
    let cleaned = trimmed.replace(/[.]+$/g, '')
    // Remover pontuação solta no início: ". Ajeite" → "Ajeite"
    cleaned = cleaned.replace(/^[.]+/g, '')
    if (cleaned) finalLines.push(cleaned)
  }

  // 13. Converter "..." (pausa dramática) de volta para double-newline
  result = finalLines.join('\n').replace(/\.\.\./g, '\n')
  result = result.replace(/\n{3,}/g, '\n\n')
  result = result.trim()

  return result
}

// ============================================================
// REPETIR ÚLTIMA PALAVRA (opcional)
// ============================================================

/**
 * Repete a última palavra de cada frase com pontuação.
 * Ex: "Olá, seja bem-vindo à nossa plataforma." 
 * → "Olá, seja bem-vindo à nossa plataforma. plataforma."
 * 
 * Isso faz o modelo articular a última palavra duas vezes,
 * garantindo que ela saia completa na segunda vez.
 */
function repeatLastWordOfSentences(text: string): string {
  return text.split('\n').map(line => {
    const trimmed = line.trim()
    if (!trimmed || trimmed.length < 5) return trimmed

    // Pega a última palavra (sem pontuação)
    const words = trimmed.split(/\s+/)
    const lastWord = words[words.length - 1].replace(/[,;:.!?]+$/, '')
    
    if (lastWord.length < 3) return trimmed // ignora palavras muito curtas

    // Repete a última palavra com ponto
    return trimmed + ' ' + lastWord + '.'
  }).join('\n')
}

// ============================================================
// QUEBRA DE FRASES LONGAS
// ============================================================

function breakLongSentences(text: string, maxWords: number): string {
  return text.split('\n').map(line => {
    const words = line.trim().split(/\s+/)
    if (words.length <= maxWords) return line.trim()

    // Procura ponto natural para quebrar
    return breakAtNaturalPoints(words, maxWords)
  }).join('\n')
}

function breakAtNaturalPoints(words: string[], maxWords: number): string {
  const breakWords = ['e', 'mas', 'porem', 'contudo', 'porque', 'pois', 'portanto', 'alem', 'tambem', 'quando', 'onde', 'como', 'para', 'com', 'mais', 'nao', 'se', 'ou']

  const sentences: string[][] = [[]]
  let currentCount = 0

  for (const word of words) {
    const cleanWord = word.toLowerCase().replace(/[,;:.!?]/g, '')
    const isBreakWord = breakWords.includes(cleanWord)

    sentences[sentences.length - 1].push(word)
    currentCount++

    // Quebra se atingiu o limite e a próxima é ponto natural
    if (currentCount >= maxWords && isBreakWord) {
      sentences.push([])
      currentCount = 0
    }

    // Hard limit
    if (currentCount >= maxWords + 5) {
      sentences.push([])
      currentCount = 0
    }
  }

  return sentences
    .map(s => s.join(' ').trim())
    .filter(s => s.length > 0)
    .join('\n')
}

// ============================================================
// SPEED FIX — Ajuste automático de velocidade
// ============================================================

/**
 * Calcula a velocidade ideal do TTS baseado no texto.
 *
 * O modelo VozPro tende a falar MUITO RÁPIDO quando:
 * - Texto tem muitas palavras difíceis (consoantes mudas, X, estrangeirismos)
 * - Texto é longo (acelera progressivamente)
 * - Texto tem travas-línguas ou termos técnicos
 *
 * Regras de ajuste:
 * - Texto curto e simples (< 50 palavras): speed = 1.0
 * - Texto médio (50-150 palavras): speed = 0.90
 * - Texto longo (> 150 palavras): speed = 0.85
 * - Texto com muitos termos difíceis: -0.05 extra
 * - Mínimo: 0.75 (não fica lento demais)
 *
 * @param text Texto que será sintetizado
 * @param baseSpeed Velocidade base (default: 1.0)
 * @returns Velocidade ajustada
 */
export function calculateAutoSpeed(text: string, baseSpeed: number = 1.0): number {
  const words = text.split(/\s+/).filter(w => w.length > 0)
  const wordCount = words.length

  // Contar indicadores de complexidade
  const complexPatterns = [
    /\b[ptgmn]\w{4,}/gi,       // palavras começando com consoante muda (psico, pneu, gno, etc.)
    /\bx/gi,                     // letra X (múltiplos sons)
    /\b[A-Z]{2,}\b/g,           // siglas (CNPJ, PDF, etc.)
    /\b\d+[\.,]\d+/g,           // números decimais
    /\bR\$/g,                    // valores monetários
    /\d+%/g,                     // porcentagens
    /\(\d{2}\)/g,               // DDD de telefone
    /[áàãâéèêíïóôõúü]/gi,       // acentos (indicam complexidade fonética)
    /\b(?:ecocardiograma|transesofágico|estenose|adenocarcinoma|eletroencefalograma|hemodiálise|azitromicina|omeprazol|dipirona|ressonância|metástase|aneurisma|insuficiência|biópsia)/gi, // termos médicos
  ]

  let complexityScore = 0
  for (const pattern of complexPatterns) {
    const matches = text.match(pattern)
    if (matches) {
      complexityScore += matches.length
    }
  }

  // Calcular velocidade base
  let speed = baseSpeed

  // Ajuste por tamanho do texto
  if (wordCount > 150) {
    speed *= 0.85
  } else if (wordCount > 80) {
    speed *= 0.90
  } else if (wordCount > 50) {
    speed *= 0.95
  }

  // Ajuste por complexidade
  const complexityRatio = complexityScore / Math.max(wordCount, 1)
  if (complexityRatio > 0.3) {
    // Mais de 30% das palavras são complexas
    speed *= 0.90
  } else if (complexityRatio > 0.15) {
    // 15-30% complexas
    speed *= 0.95
  }

  // Limites
  speed = Math.max(0.75, Math.min(1.0, speed))

  // Arredondar para 2 casas decimais
  speed = Math.round(speed * 100) / 100

  return speed
}

/**
 * Retorna a velocidade ajustada e informações de debug.
 */
export function getAutoSpeedInfo(text: string, baseSpeed: number = 1.0): {
  speed: number
  wordCount: number
  complexityScore: number
  adjustmentReason: string
} {
  const words = text.split(/\s+/).filter(w => w.length > 0)
  const wordCount = words.length

  const complexPatterns = [
    /\b[ptgmn]\w{4,}/gi,
    /\bx/gi,
    /\b[A-Z]{2,}\b/g,
    /\b\d+[\.,]\d+/g,
    /\bR\$/g,
    /\d+%/g,
    /\(\d{2}\)/g,
    /[áàãâéèêíïóôõúü]/gi,
    /\b(?:ecocardiograma|transesofágico|estenose|adenocarcinoma|eletroencefalograma|hemodiálise|azitromicina|omeprazol|dipirona|ressonância|metástase|aneurisma|insuficiência|biópsia)/gi,
  ]

  let complexityScore = 0
  for (const pattern of complexPatterns) {
    const matches = text.match(pattern)
    if (matches) complexityScore += matches.length
  }

  const speed = calculateAutoSpeed(text, baseSpeed)
  const complexityRatio = complexityScore / Math.max(wordCount, 1)

  let reason = ''
  if (wordCount > 150) reason += 'Texto longo, '
  else if (wordCount > 80) reason += 'Texto médio, '
  else reason += 'Texto curto, '

  if (complexityRatio > 0.3) reason += 'alta complexidade'
  else if (complexityRatio > 0.15) reason += 'complexidade média'
  else reason += 'complexidade baixa'

  return { speed, wordCount, complexityScore, adjustmentReason: reason }
}
