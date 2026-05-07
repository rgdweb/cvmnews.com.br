# ===========================================
# PIPELINE DE PRONÚNCIA PT-BR — VozPro
# Data: 07/05/2026
# Última atualização: Sessão de melhorias de pronúncia
# ===========================================

## VISÃO GERAL

O VozPro possui um pipeline de 3 camadas para otimizar a pronúncia do TTS em PT-BR.
**Tudo funciona de forma invisível ao usuário** — ele digita o texto normal, clica em "Gerar",
e o sistema aplica as correções por baixo dos panos antes de enviar ao motor TTS.
O texto na textarea NÃO é alterado visualmente.

## ARQUITETURA DO PIPELINE

```
Texto do Usuário (textarea)
    │
    ├── [pronunciationOptimization = ON] (switch na UI, default: ON)
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ CAMADA 1: optimizePronunciation()                               │
│ Arquivo: src/lib/pronunciation-optimizer.ts                     │
│ Latência: 0ms (regex + dicionário local)                        │
│                                                                  │
│ • Dicionário de 403+ palavras problemáticas                     │
│ • Pré-processador X contextual (6 sons)                         │
│ • Expansão de números, moeda, datas, horários, telefones       │
│ • Correção de artigos O/A após pontuação                        │
│ • Siglas, abreviações, estrangeirismos                          │
│ • URLs e e-mails por extenso                                    │
│ • Usa notação [pronúncia] nativa do VozPro/k2-fsa              │
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ CAMADA 2: preprocessTTS()                                       │
│ Arquivo: src/lib/tts-text-preprocessor.ts                       │
│ Latência: 0ms (regex local)                                     │
│                                                                  │
│ • Converte pontuação forte (. ! ?) em newlines                  │
│ • Vírgulas → 2 espaços (micro-pausa)                            │
│ • Ponto e vírgula / dois pontos → newlines                      │
│ • Quebra frases longas (>20 palavras) em pontos naturais        │
│ • Remove reticências (TTS tenta falar "...")                    │
│ • Reticências → newline (pausa natural)                         │
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
┌─────────────────────────────────────────────────────────────────┐
│ CAMADA 3: calculateAutoSpeed()                                  │
│ Arquivo: src/lib/tts-text-preprocessor.ts                       │
│ Latência: 0ms (cálculo local)                                   │
│                                                                  │
│ • Calcula velocidade ideal baseado no texto                     │
│ • Texto curto e simples (<50 palavras): speed = baseSpeed       │
│ • Texto médio (50-80): speed × 0.95                             │
│ • Texto longo (80-150): speed × 0.90                            │
│ • Texto muito longo (>150): speed × 0.85                        │
│ • Complexidade alta (>30% palavras difíceis): speed × 0.90      │
│ • Complexidade média (15-30%): speed × 0.95                     │
│ • Mínimo: 0.75, Máximo: 1.0                                     │
│ • Respeita o slider do usuário como base                        │
└─────────────────────────────────────────────────────────────────┘
    │
    ▼
  Texto otimizado → enviado ao TTS (invisível ao usuário)
```

## FLUXO NO CÓDIGO (page.tsx)

```typescript
// Linha ~472 em src/app/page.tsx
let textToSend = text.trim()
let effectiveSpeed = speed  // velocidade do slider do usuário

if (pronunciationOptimization) {
  // Camada 1: Otimização de pronúncia (dicionário + regex + X contextual)
  textToSend = optimizePronunciation(textToSend)
  // Camada 2: Pré-processamento TTS (quebra frases, micro-pausas, newlines)
  textToSend = preprocessTTS(textToSend)
  // Camada 3: Ajuste automático de velocidade (reduz se texto é complexo)
  effectiveSpeed = calculateAutoSpeed(textToSend, speed)
}

// textToSend vai para o TTS, `text` (variável do textarea) não é alterado
// effectiveSpeed substitui o speed: 1.0 hardcoded anterior
```

## DICIONÁRIO DE PRONÚNCIA (403+ entradas)

### Categorias cobertas:

| Categoria | Qtd aprox | Exemplos |
|-----------|-----------|----------|
| Siglas/acrônimos | ~40 | API→[a p i], CPF→[cê pê éfe], IBGE→[i bê gê i] |
| Estrangeirismos tech | ~80 | marketing→[marqueting], software→[softeuér], download→[daunloud] |
| Estrangeirismos comuns (NOVO) | ~80 | friend→[frende], cool→[cule], night→[naite] |
| Abreviações | ~35 | Sr.→[Senhor], Dr.→[Doutor], Av.→[Avenida] |
| Consoantes mudas | ~20 | ptialismo→[petialismo], cpt→[cê pê tê], gnomo→[nomo] |
| H mudo | ~2 | hérnia→[érnia] (apenas as problemáticas) |
| Termos médicos | ~15 | ecocardiograma, hemodiálise, azitromicina |
| Nomes próprios | ~10 | Wolski→[Volski], L'Oréal→[Loreal] |
| X contextual | ~100 | xarope→[charope], táxi→[tácsi], exemplo→[ezemplo] |

### Palavras PT-BR que NÃO devem ser mapeadas:
- `no` — preposição comum ("no dia", "no carro")
- `data` — "data de nascimento"
- `real` — moeda e adjetivo
- `pro` — contração de "para o"
- `super` — já pronunciado corretamente
- `hoje`, `hora`, `hotel`, `homem`, `humor` — H mudo já funciona no TTS

## PRÉ-PROCESSADOR X (6 sons contextuais)

O X em português tem 6 sons possíveis. O dicionário cobre os casos principais:

| Som | Contexto | Exemplos |
|-----|----------|----------|
| CH | Início de palavra, ENX- | xarope→[charope], enxada→[enchada], baixo→[baicho] |
| Z | EX- antes de vogal | exemplo→[ezemplo], existir→[ezistir], exato→[ezato] |
| S | EX- antes de consoante | extensão→[estensão], explicar→[esplicar], expressão→[espressão] |
| SS | México, mexer | México→[Méssico], mexer→[messer] |
| KS | Entre vogais, final | táxi→[tácsi], complexo→[complekso], tóxico→[tóksico] |
| S | Fallback antes de consoante | Regex: x + consoante → s + consoante |

## ERROS REPORTADOS NOS TESTES (07/05/2026)

### Teste realizado pelo usuário com 13 trechos:

| Erro | Causa raiz | Status da correção |
|------|------------|-------------------|
| "ptialismo" → "tialismo" (dropou P) | Consoante muda inicial | ✅ Dicionário: ptialismo→[petialismo] |
| "cpt" → "cp" (dropou P) | Consoante muda em sigla | ✅ Dicionário: cpt→[cê pê tê] |
| X pronúncias erradas | Modelo não distingue os 6 sons | ✅ Pré-processador X contextual |
| Travou na letra "O" | Artigo isolado lido como letra Ó | ✅ Artigo → [o] após pontuação |
| Inglês tudo errado | TTS usa fonética PT-BR em inglês | ✅ 80+ estrangeirismos mapeados |
| Números complexos | TTS lê dígitos literalmente | ✅ Expansão por extenso |
| Fala muito rápido | Speed hardcoded 1.0 | ✅ calculateAutoSpeed() integrado |
| "O" antes de nome não falado | Artigo engolido antes de maiúscula | ✅ Regex: O→[o] antes de nomes próprios |

## ARQUIVOS-CHAVE

| Arquivo | Função |
|---------|--------|
| `src/lib/pronunciation-optimizer.ts` | Dicionário + regex + X preprocessor + números |
| `src/lib/tts-text-preprocessor.ts` | Quebra de frases + micro-pausas + auto speed |
| `src/lib/asr-validator.ts` | Validação ASR pós-geração |
| `src/app/page.tsx` | UI principal + integração do pipeline |
| `src/app/api/optimize-pronunciation/route.ts` | API route LLM (fallback, pouco usada) |
| `src/app/api/omnivoice-generate/route.ts` | API route TTS (passa speed) |

## NOTA IMPORTANTE SOBRE OTIMIZAÇÃO INVISÍVEL

O usuário pediu explicitamente que as correções sejam INVISÍVEIS:
- O texto na textarea NÃO muda quando o usuário clica "Gerar"
- As otimizações são aplicadas APENAS ao `textToSend` (variável interna)
- O `text` (estado do textarea) permanece intacto
- O switch "Otimização de pronúncia IA" liga/desliga tudo

## CAMADA LLM (fallback, raramente usada)

A API route `/api/optimize-pronunciation` usa LLM (z-ai-web-dev-sdk) como fallback
para termos não cobertos pelo regex. Atualmente o fluxo principal NÃO usa esta rota —
tudo é processado client-side pelas Camadas 1-3. A rota existe para uso futuro ou
caso se queira adicionar um passo de LLM no pipeline.

## COMO TESTAR

1. Acesse https://omnivoice-umber.vercel.app/
2. Digite texto com desafios (X, números, estrangeirismos, consoantes mudas)
3. Com "Otimização de pronúncia IA" LIGADA → texto otimizado vai ao TTS
4. Com "Otimização de pronúncia IA" DESLIGADA → texto cru vai ao TTS
5. Compare os resultados

## GIT — COMMITS DESTA SESSÃO

```
ec4b297  backup: estado antes das melhorias de pronúncia PT-BR
[próximo] feat: pipeline pronúncia 3 camadas + dicionário expandido + auto speed
```

## PRÓXIMOS PASSOS SUGERIDOS

1. **Testar com o texto rigoroso** — re-executar os 13 trechos de teste
2. **Ajustar pronúncias individuais** — se alguma transliteração ainda soa errada
3. **Considerar LLM no pipeline** — para nomes próprios incomuns que o dicionário não cobre
4. **Medir latência** — garantir que preprocessTTS + optimizePronunciation < 50ms
5. **Expandir dicionário** — conforme novos erros forem identificados
6. **"showroom"** — a entrada `[chorume]` está incorreta, deveria ser `[xourume]`
