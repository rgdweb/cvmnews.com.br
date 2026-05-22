---
Task ID: 1
Agent: main
Task: Investigar e corrigir corte de áudio no final das frases geradas pelo OmniVoice

Work Log:
- Fez auditoria completa de TODO o codebase procurando applyFadeOut, fadeOutMs, crossfadeMs, trimEndSilence
- Confirmou que todos os fades e trims anteriores foram corretamente desativados nos commits f828298 e 7841596
- Identificou que `postprocess_output: true` na linha 454 de tunnel-generate/route.ts fazia o próprio modelo OmniVoice cortar silêncio do final — cortando a última sílaba junto
- Alterou `postprocess_output` de `true` para `false` com comentário explicativo
- Commit c40cd6a enviado ao GitHub/Vercel

Stage Summary:
- Arquivo editado: src/app/api/tunnel-generate/route.ts (linha 454)
- Mudança: postprocess_output true → false
- Commit: c40cd6a — já enviado ao GitHub, Vercel deve auto-deploy
---
Task ID: 1
Agent: Main
Task: Fix audio cutting at end for long text (>280 chars) via auto-chunking

Work Log:
- Analyzed diagnostic data: 351 chars → 19.9s audio (should be 28.1s = 29% shorter)
- Confirmed root cause: OmniVoice postprocess_output=true cuts ~8.2s of speech content for text >280 chars
- Added chunkByCharLimit() to tts-chunker.ts: splits text into max 250-char chunks at commas, punctuation, conjunctions
- Re-enabled chunking in route.ts for text >280 chars (auto, no manual toggle needed)
- Modified generateChunk() to add delay after SSE + silence padding per chunk
- Modified generateWithChunking() to use char-limit chunking + light concatenation config (no trim, no normalize)
- Updated page.tsx debug panel to show chunking info
- Committed and pushed to Vercel (8241d91)

Stage Summary:
- Key fix: Auto-chunking for text >280 chars, single-shot for text <=280 chars
- Each chunk is ~250 chars (where postprocess works fine)
- Light concatenation config avoids artifacts that caused previous chunking to be disabled
- Debug panel now shows chunk count, text per chunk, pause durations

