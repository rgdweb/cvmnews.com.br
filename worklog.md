---
Task ID: 0 - REGISTRO PERMANENTE DE CONFIGURAÇÃO CRÍTICA
Agent: main
Task: Registrar todas as configurações que funcionaram perfeitamente

## ⚠️ REGRAS DE OURO — NUNCA VIOLAR ⚠️

### 1. MODO LIMPO — TEXTO DIRETO SECO
Quando o usuário diz "sem efeito", "texto direto", "tava indo otimo antes",
SIGNIFICA: texto vai DIRETO pro modelo OmniVoice sem NENHUM processamento.

O que NÃO fazer (causa voz delirada / velocidade variando):
- ❌ NÃO usar chunking (divide texto, cada pedaço sai com velocidade diferente)
- ❌ NÃO usar preprocessTTS() (adiciona/reordena pontuação)
- ❌ NÃO usar optimizePronunciation() (substitui palavras por fonemas)
- ❌ NÃO usar processControlTags() (adiciona tags de controle no texto)
- ❌ NÃO usar parseSSML() (converte SSML → texto modificado)

O que fazer (modo que funcionou perfeitamente):
- ✅ Texto do usuário vai EXATAMENTE como digitou pro Gradio
- ✅ stripSSMLForTTS() OK — só remove tags se tiver, não modifica texto
- ✅ refText SEMPRE vazio '' (causa alucinação se preenchido)
- ✅ instruct limitado a 3 partes (mais que isso causa delírio)
- ✅ Single-shot: texto inteiro de uma vez (velocidade consistente)
- ✅ speed = parseFloat() || 1.0 (sempre float, nunca int)
- ✅ speed range no slider: min=0.8, max=1.3, step=0.05

### 2. PARÂMETROS DO GRADIO (ORDEM IMPORTANTE)
gradioBaseData = [
  text,           // índice 0: texto limpo
  language,       // índice 1: 'Auto' ou 'Portuguese'
  refAudio,       // índice 2: { path, orig_name, mime_type, is_stream, meta } ou null
  '',             // índice 3: refText — SEMPRE VAZIO (causa alucinação!)
  safeInstruct,   // índice 4: instruct limitado a 3 partes separadas por vírgula
  32,             // índice 5: numStep
  2.0,            // índice 6: guidanceScale
  true,           // índice 7: denoise
  1.0,            // índice 8: speed (sempre float!)
  null,           // índice 9: duration
  true,           // índice 10: preprocess_prompt
  true,           // índice 11: postprocess_output (padrão Gradio = antiruido)
]

### 3. PIPELINE DE GERAÇÃO DE ÁUDIO
- Texto <= 800 chars → SEMPRE single-shot (1 chamada, velocidade consistente)
- Texto > 800 chars → single-shot primeiro, chunking SÓ se falhar
- Chunking NUNCA como padrão (causa velocidade variando: lento/rápico alternado)

### 4. CHUNKING (APENAS COMO ÚLTIMO RECURSO)
Se chunking for necessário (texto gigante >800 chars):
- Usar chunkByCharLimit(text, 250)
- Adicionar silêncio MÍNIMO de 250ms entre chunks
- postprocess_output=true (padrão Gradio)
- Silêncio entre chunks = respiração natural

### 5. SPEED SLIDER
- Min: 0.8, Max: 1.3, Step: 0.05, Default: 1.0
- Fora desse range (0.5-1.5) causa distorção
- Sempre enviar como float pro backend (1.0 não 1)

### 6. TUNNEL
- Programa: cloudflared (NÃO localtunnel)
- Caminho: C:\Users\Administrador\AppData\Local\Microsoft\WinGet\Links\cloudflared.exe
- Registro: POST JSON { tunnelUrl: "..." } para https://sorteiomax.com.br/omnivoice/update_tunnel.php
- Auth: vozpro_tunnel_2024
- Health check: verificar se tunnel responde HTTP 200 antes de usar
- Upload retry: 3 tentativas com 3s delay entre cada
- Download retry: 3 tentativas com validação WAV (header data size == bytes reais)

### 7. GPU/SERVIDOR LOCAL
- Modelo: OmniVoice (k2-fsa/F5-TTS)
- Porta: 7860 (Gradio)
- GPU: RTX 3060 12GB
- Script: omnivoice_gpu.py (lançado via iniciar_monitor.bat)
- Auto-restart: diagnostico_auto_restart.py (reinicia após 60min idle)

### 8. HISTÓRICO DE ERROS QUE NÃO DEVEM SE REPETIR
| Erro | Causa | Lição |
|------|-------|-------|
| Voz "delirada" | Chunking divide texto, velocidade diferente por pedaço | Sempre single-shot para textos normais |
| Velocidade variando (lento/rápico) | Chunking com threshold muito baixo (250 chars) | Single-shot para <=800 chars |
| Tunnel "fetch failed" | start_tunnel.ps1 enviava `url=` em vez de `tunnelUrl` | POST JSON com chave `tunnelUrl` |
| Tunnel URL antiga no PHP | Health check faltando | Verificar tunnel vivo antes de usar |
| RAM mostrando 0.0 GB | PowerShell Format-List parsing | Comando simples direto |
| Fala alucinada ("ba", "to") | refText preenchido + instruct longo | refText SEMPRE vazio, instruct max 3 partes |
| Speed bugando a fala | Range 0.5-1.5 muito largo | Range 0.8-1.3 |
| Speed como int | `speed = 1` enviado como integer | `speed = 1.0` sempre float |
| Comentário mentiroso | Código dizia postprocess=false mas era true | Ler código real, não comentários |

---
Task ID: 2
Agent: main
Task: Fix tunnel URL not being registered - causing "fetch failed" on upload

Work Log:
- start_tunnel.ps1: Changed from GET `?url=` to POST JSON body `{ tunnelUrl: $cfUrl }` with 3 retries
- tunnel-generate/route.ts: Added health check + upload retry
- PHP on sorteiomax.com.br accepts POST JSON with `tunnelUrl` key

Stage Summary:
- User needs to copy updated start_tunnel.ps1 to PC
- After restart, tunnel URL registers correctly on PHP server
- Vercel API validates tunnel is alive before using it

---
Task ID: 3
Agent: main
Task: Fix speed slider range and voice consistency

Work Log:
- Speed slider: min=0.5 max=1.5 → min=0.8 max=1.3 step=0.05
- useChunking default: true → false (single-shot mode)
- speed default: int 1 → float 1.0
- Build passed, deployed to Vercel

Stage Summary:
- Speed range safe (0.8-1.3)
- Single-shot as default mode
- User reported voice inconsistency after deploy

---
Task ID: 4
Agent: main
Task: Full project audit — fix voice "delirada" and inconsistency

Work Log:
- Read ALL 104 source files in src/
- Compared frontend↔backend parameter consistency
- Found BUG 1: Comment said postprocess=OFF but code was ON
- Found BUG 2: Random single-shot→chunking fallback caused inconsistency
- Found BUG 3: chunkByCharLimit had pauseAfterMs=0 always (no silence between chunks)
- Fixed: Corrected comments to match actual code
- Fixed: Smart pipeline (<=250 chars single-shot, >250 chunking)
- Fixed: Added minimum 250ms silence between chunks
- Created /api/diagnose endpoint for real-time monitoring
- Build passed, deployed to Vercel

Stage Summary:
- 3 critical bugs found and fixed
- Pipeline now routes by text length
- New /api/diagnose endpoint available

---
Task ID: 5
Agent: main
Task: Fix voice speed variation (lento/rápico alternado)

Work Log:
- User reported: "voz fica mistura de velocidade, lento e acelera, sem padrão"
- Root cause: Chunking threshold 250 chars was TOO AGGRESSIVE
- Chunking divides text → each chunk gets different speed from TTS model
- When concatenated: speed varies between chunks (lento/rápico/lento/rápido)
- Fix: Changed to SINGLE-SHOT ONLY for texts <=800 chars
- Chunking only as last resort for texts >800 chars that fail single-shot
- Texts <=800 that fail → return error (don't attempt chunking)

Stage Summary:
- Pipeline: SINGLE-SHOT for <=800 chars (no chunking = consistent speed)
- Chunking disabled for normal texts
- Speed variation should be eliminated

---
Task ID: 6
Agent: main
Task: Tunnel offline — user reported "fetch failed"

Work Log:
- Debug shows: tunnel URL registered but "fetch failed" when connecting
- This means cloudflared process is NOT running on the user's PC
- User needs to restart tunnel on local machine (start_tunnel.ps1 or iniciar_monitor.bat)
- This is NOT a code issue — it's a connectivity issue

Stage Summary:
- Tunnel is down (cloudflared not running)
- User needs to run iniciar_monitor.bat on the local PC
- After restart, Vercel will be able to connect to Gradio via tunnel
---
Task ID: 1
Agent: main
Task: Limpar pipeline TTS para modo LIMPO single-shot (sem chunking, sem efeitos)

Work Log:
- Leu tunnel-generate/route.ts completo (908 linhas) — identificou TODOS os efeitos/processamentos
- Removeu TODOS os imports de chunking, ASR, SSML, text preprocessor, audio trimmer, audio concatenator
- Removeu funções: generateWithChunking, generateChunk, appendWavSilence, buildSimpleWavHeader, parseWavHeaderSimple
- Removeu lógica de fallback chunking (TEXT_THRESHOLD=800) e pipeline de seleção
- Removeu validação ASR do resultado final
- Removeu SSML stripping (agora texto vai direto pro Gradio)
- Manteve apenas: tunnel URL discovery, upload com retry, submit job, SSE stream, download com retry + WAV check
- Pipeline agora é: texto inteiro → Gradio → audio → return (zero processamento)
- Atualizou diagnose/route.ts para refletir modo limpo
- Verificou frontend: useChunking=false já enviado, sem preprocessTTS ativo
- Build limpo: ✓ Compiled successfully (zero erros/warnings)
- Push para GitHub: b66f033

Stage Summary:
- tunnel-generate/route.ts: 908 linhas → ~280 linhas (cortou ~70% do código)
- Pipeline: 100% single-shot, sem chunking, sem ASR, sem preprocess, sem trim
- Parâmetros Gradio mantidos iguais ao demo localhost:7860
- Deploy via git push (Vercel auto-deploy)
---
Task ID: 2
Agent: main
Task: Investigar tunnel parando + voz oscilando ("bebada")

Work Log:
- Analisou iniciar_monitor.bat — mata python.exe, node.exe, cloudflared.exe na inicialização (ok, é limpeza)
- Analisou fechar.bat / fechar_tudo.bat — mata processos (normal)
- Analisou start_tunnel.ps1 — registro de tunnel OK (POST JSON)
- Analisou diagnostico.py (monitor automático) — ENCONTROU BUG CRÍTICO
- Comparou parâmetros PHP generate-omnivoice.php vs tunnel-generate/route.ts — IDÊNTICOS
- Desativou auto-restart no diagnostico.py (download/ e local-server/)
- Corrigiu do_restart() para matar por PID na porta 7860 em vez de taskkill /IM python.exe
- Push: 5344c1c

Stage Summary:
- BUG CRÍTICO ENCONTRADO: diagnostico.py do_restart() fazia taskkill /F /IM python.exe
  → Isso matava o PRÓPRIO monitor no meio do restart
  → OmniVoice e tunnel MORRIAM e NUNCA VOLTAVAM
  → Sistema ficava 100% offline sem o usuário saber
- CORREÇÃO: auto_restart_enabled = False + kill por PID ao invés de nome de processo
- Parâmetros Gradio: idênticos entre PHP e route.ts (postprocess=true, preprocess=true, denoise=true)
- A voz "bebada" provavelmente era causada por tunnel instável (morria e não voltava)
