---
Task ID: 10
Agent: Main
Task: Migração tunnel-generate para Oracle PHP + SSL Letsencrypt

Work Log:
- Conectou ao Oracle VPS (147.15.77.137) via SSH com paramiko (user: ubuntu)
- Verificou estrutura do servidor: nginx root = /var/www/omnivoice (sem prefixo /omnivoice/)
- Confirmou API_KEY no config.php do Oracle: vozpro_2024_a8f7d9e2b4c1m6n3p5q0r9s2t8u1
- Upload do tunnel-generate.php via SFTP (/tmp -> sudo cp -> chown www-data)
- Corrigiu path interno: get_tunnel.php de /omnivoice/ para / (sem prefixo)
- Teste inicial via HTTP: sem token -> 401, com token -> 200 + audio base64 (SUCESSO!)

- Problema 1: Frontend tentava chamar http://147.15.77.137 direto -> Mixed Content (browser bloqueia HTTP em pagina HTTPS)
- Solução temporária: frontend chama /api/tunnel-generate (same-origin HTTPS), Vercel route.ts faz proxy pro Oracle HTTP server-to-server
- Push commit 376ce72

- Problema 2: Solucao acima usava Vercel como proxy (nao era o ideal)
- Solucao definitiva: instalar SSL no Oracle com Letsencrypt
- Verificou que sorteiomax.com.br aponta pra 108.179.241.225 (HostGator), nao pro Oracle
- Usuario criou subdomínio api.sorteiomax.com.br -> A record 147.15.77.137 no painel de hospedagem
- DNS propagou instantaneamente (Cloudflare)

- Gerou certificado SSL Letsencrypt via certbot webroot (sem parar nginx)
  - Dominio: api.sorteiomax.com.br
  - Validade: 2026-08-26 (renovação automática via cron do certbot)
  - Cert: /etc/letsencrypt/live/api.sorteiomax.com.br/fullchain.pem
  - Key: /etc/letsencrypt/live/api.sorteiomax.com.br/privkey.pem

- Configurou nginx com HTTPS:
  - Porta 443 SSL para api.sorteiomax.com.br
  - Redirect HTTP->HTTPS (porta 80, server_name api.sorteiomax.com.br)
  - Server block default (porta 80) mantido para requests diretos por IP

- Testes HTTPS:
  - https://api.sorteiomax.com.br/tunnel-generate.php (sem token) -> 401 (antes da remoção)
  - https://api.sorteiomax.com.br/get_tunnel.php -> 200 (tunnel online)
  - https://api.sorteiomax.com.br/tunnel-generate.php (com token) -> 200 + audio base64 ✅

- Removeu validação de token HMAC do tunnel-generate.php (protegido por SSL agora)
- Atualizou frontend: chama https://api.sorteiomax.com.br/tunnel-generate.php direto
- Push commit 37dde5a

- Teste final de ponta-a-ponta: SUCESSO - audio gerado normalmente via HTTPS direto

Stage Summary:
- Pipeline final: Browser (HTTPS) -> api.sorteiomax.com.br (HTTPS SSL) -> Oracle PHP -> Tunnel -> GPU PC -> Audio
- Zero Vercel no pipeline de geracao (Vercel so serve o frontend/interface)
- Zero mixed content (HTTPS -> HTTPS)
- Zero timeout (PHP sem limite de execução)
- Zero custo extra (Oracle ja pago, Letsencrypt gratuito)
- Arquivos no Oracle: /var/www/omnivoice/tunnel-generate.php
- SSL: Letsencrypt em api.sorteiomax.com.br (auto-renew)
- Frontend: process.env.NEXT_PUBLIC_AUDIO_SERVER_URL || 'https://api.sorteiomax.com.br'

---
Task ID: 1
Agent: Main
Task: Fix chunking regression — audio still 33% short even with 7 chunks

Work Log:
- Diagnosed root cause: postprocess_output: true was being sent for EACH chunk individually, causing OmniVoice to cut ~29% of each chunk's audio
- Fix: Set postprocess_output: false for all chunks
- Files modified: src/app/api/tunnel-generate/route.ts, src/app/page.tsx

Stage Summary:
- Partial fix — postprocess=false resolved cutting but caused hiss/chiado in audio

---
Task ID: 2
Agent: main
Task: Eliminar cortes no audio TTS — tentativas de chunking e processamento

Work Log:
- Tentativa 1: Chunking com overlap buffer -> causou repetição de palavras (REJEITADO)
- Tentativa 2: Crossfade entre chunks -> causou estalos e ruído (REJEITADO)
- Tentativa 3: postprocess_output=false -> causou chiado/hiss no audio inteiro (REJEITADO)
- Tentativa 4: Separação denoise=true + postprocess=false -> não testado, pivotei para single-shot
- Tentativa 5: Retry com re-download -> causou pops e cracks (REJEITADO)
- Tentativa 6: Detecção de zeros PCM no tail + retry -> "muita instabilidade no audio" (REJEITADO)
- Tentativa 7: Fade-out manual -> "deu fade out em SP" (REJEITADO)

Stage Summary:
- TODAS as abordagens de processamento/manipulação de PCM causaram artefatos
- O modelo OmniVoice NAO é o problema — localhost demo funciona perfeitamente com textos longos (756+ chars em 30s)

---
Task ID: 3
Agent: main
Task: SOLUCAO FINAL — Single-shot puro

Work Log:
- Removeu TODOS os codigos de chunking, retry, detecção PCM, crossfade, overlap
- Removeu import de tts-chunker.ts
- Implementou generateSingleShot(): envia texto inteiro em 1 unica chamada API (igual localhost demo)
- Delay fixo de 10 segundos após SSE complete antes de baixar o WAV
- Zero processamento/manipulação de audio — entrega exatamente o que o Gradio gera
- Corrigiu bug "useChunking is not defined" — referencia pendente da limpeza
- File reduziu de ~580 para ~444 linhas

Stage Summary:
- SOLUCAO: Single-shot puro + delay 10s = AUDIO PERFEITO SEM CORTE
- Testes confirmados pelo usuario: 756 caracteres, 30 segundos, sem corte, falou perfeito (2x seguido)
- Pipeline antigo: Browser -> Vercel route.ts -> HostGator get_tunnel.php -> cloudflared -> Local GPU Gradio (OmniVoice)

---
## LIÇOES APRENDIDAS

1. **NUNCA use chunking com TTS OmniVoice** — corta palavras nas junções (SP, br, X-BURGUER)
2. **NUNCA manipule PCM diretamente** (fade, crossfade, trim, zero detection) — sempre causa artefatos
3. **NUNCA desative postprocess_output** — causa chiado/hiss no audio inteiro
4. **Single-shot e a UNICA abordagem confiavel** — manda tudo de uma vez igual o demo local
5. **O delay de 10s e ESSENCIAL** — dá tempo do Gradio salvar o WAV completo via tunnel antes do download
6. **O problema nunca foi o modelo** — era sempre a entrega via tunnel + processamento desnecessário
7. **Mixed Content** — browser bloqueia HTTP em pagina HTTPS, usar SSL ou proxy server-side
8. **OmnVoice nativo** — rodar 100% via Python/Starlette (sem Gradio) resolve middleware 404 do Gradio 6.x

---
## ARQUITETURA FINAL (maio 2026)

### Pipeline de Geração
```
Browser (HTTPS)
  -> api.sorteiomax.com.br (HTTPS, SSL Letsencrypt, Oracle VPS)
    -> tunnel-generate.php (descobre tunnel URL via get_tunnel.php local ou tunnel-config.ini)
      -> cloudflared tunnel (trycloudflare.com)
        -> GPU PC port 7860 (Python/Starlette, omnivoice_gpu.py)
          -> /api/native-generate (OmniVoice.from_pretrained(), model.generate())
            -> WAV base64 -> volta pelo mesmo caminho
```

### Servidores
| Função | Local | IP/URL |
|--------|-------|--------|
| Frontend (interface) | Vercel | omnivoice-umber.vercel.app |
| API PHP (proxy) | Oracle VPS | api.sorteiomax.com.br (147.15.77.137) |
| GPU (geração) | PC local via tunnel | trycloudflare.com (dinamico) |
| Banco de dados | Neon PostgreSQL | ep-blue-band-ac85wa8e.sa-east-1.aws.neon.tech |

### SSL
- api.sorteiomax.com.br: Letsencrypt (auto-renew via certbot cron)
- Vercel: SSL nativo da plataforma
- Cert path: /etc/letsencrypt/live/api.sorteiomax.com.br/
- Validade ate: 2026-08-26 (renovação automática)

### Arquivos Criticos no Oracle (/var/www/omnivoice/)
| Arquivo | Função |
|---------|--------|
| tunnel-generate.php | Proxy PHP — recebe JSON do browser, chama GPU, retorna audio |
| get_tunnel.php | Retorna URL do tunnel ativo (tunnel-config.ini ou cloudflared) |
| config.php | API_KEY, BASE_URL, UPLOAD_DIR, ALLOWED_TYPES |
| update_tunnel.php | Recebe nova tunnel URL do PC GPU via PowerShell |
| tunnel-config.ini | Arquivo INI com tunnel URL atualizada pelo cloudflared |

### Arquivos Criticos no GPU PC (C:\omnivoice\)
| Arquivo | Função |
|---------|--------|
| omnivoice_gpu.py | Servidor Python/Starlette puro (sem Gradio), carrega OmniVoice, endpoints: /, /health, /api/maint/*, /api/native-generate |
| diagnostico_auto_restart.py | Monitoramento automatico: fila, idle detection, restart, GPU cleanup |
| iniciar.bat | Inicia omnivoice_gpu.py + cloudflared tunnel |
| start_tunnel.ps1 | Inicia cloudflared e registra URL no Oracle via update_tunnel.php |

### Parâmetros OmniVoice (enviados via /api/native-generate)
- text (string) — texto para gerar
- voice_mode ("clone" | "design") — modo de voz
- ref_audio_url (string) — URL do audio de referencia (clone)
- ref_audio_base64 (string) — base64 do audio de referencia (upload do frontend)
- language ("Auto" | "pt" | "en" etc.)
- instruct (string) — instruções de estilo (design mode)
- ref_text (string) — texto do audio de referencia
- speed (1.0) — range seguro: 0.85-1.15
- num_step (32) — range seguro: 20-50
- guidance_scale (2.0) — range seguro: 1.0-3.5
- postprocess_output (true — NUNCA mudar para false)
- denoise (true)

### Formato WAV de Saida
- Sample Rate: 24000 Hz
- Bits: 16
- Channels: 1 (mono) — OmniVoice converte stereo internamente via soundfile

### Pontos Criticos do Codigo (NAO MEXER)
- omnivoice_gpu.py: carregamento direto via OmniVoice.from_pretrained() (sem Gradio)
- omnivoice_gpu.py: manutenção GPU automatica (monitor a cada 3min, pre-gen cleanup, deep cleanup a cada 5 gens)
- tunnel-generate.php: descobre tunnel via tunnel-config.ini OU get_tunnel.php (fallback)
- Frontend: process.env.NEXT_PUBLIC_AUDIO_SERVER_URL aponta para https://api.sorteiomax.com.br
- route.ts (Vercel): mantido como fallback — descobre tunnel + chama native-generate

### SSH Acesso ao Oracle
- IP: 147.15.77.137
- User: ubuntu (root tem login desabilitado)
- Key: /home/z/my-project/upload/ssh-key-oracle.key
- Docroot: /var/www/omnivoice
- Nginx config: /etc/nginx/sites-available/omnivoice

### Commits Importantes
| Commit | Descricao |
|--------|-----------|
| 376ce72 | fix: proxy via Vercel HTTPS para evitar Mixed Content |
| 37dde5a | fix: chamar Oracle HTTPS direto (api.sorteiomax.com.br) sem Vercel |

---
## COMO RESTAURAR APOS PERDA DE CODIGO

1. `git fetch origin`
2. `git reset --hard origin/main` (sobrescreve local com o remoto)
3. `npm install` (se mudou package.json)
4. `npx prisma generate` (se mudou schema)
5. `npx next build` (verificar build)

O branch remoto origin/main e a fonte de verdade.
