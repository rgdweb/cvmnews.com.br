---
Task ID: 1
Agent: Main Agent
Task: Auditoria completa dos arquivos PHP do OmniVoice + correção do "Failed to fetch"

Work Log:
- Leitura e comparação de todos os arquivos do pacote omnivoice-fix-final com versões originais
- Identificação de 5 bugs críticos que causaram o "Failed to fetch"
- Criação de arquivos corrigidos: config.php, .htaccess, generate-direct.php, generate.php, generate-omnivoice.php
- Geração do pacote omnivoice-fix-audit.zip com 20+ arquivos

Stage Summary:
- BUG #1 CRÍTICO: config.php define HF_SPACE_URL como '' (vazio). O generate-direct.php verifica defined() que retorna TRUE, usando URL vazia em vez do fallback.
- BUG #2 CRÍTICO: .htaccess usa "Header set" (sem "always") — CORS só é enviado em respostas 200 OK. Respostas de erro (401/400/500) ficam sem CORS → browser bloqueia → "Failed to fetch"
- BUG #3 CRÍTICO: generate-direct.php e generate.php NÃO usam TUNNEL_URL do tunnel-config.ini, ignorando a URL dinâmica do cloudflared
- BUG #4: .htaccess mistura sintaxe Apache 2.2 (Order deny,allow) com 2.4 (Require all denied)
- BUG #5: generate-direct.php alterou parâmetros do Gradio que estavam funcionando (instruct, url, size)
- SOLUÇÃO: Nova função getTtsUrl() em config.php que tenta TUNNEL_URL > HF_SPACE_URL > get_tunnel.php > fallback
- SOLUÇÃO: .htaccess com "Header always set" + sintaxe Apache 2.4 consistente
- SOLUÇÃO: generate-direct.php usa getTtsUrl() + restaura parâmetros originais do Gradio
- Pacote: /home/z/my-project/download/omnivoice-fix-audit.zip (53KB, 20 arquivos)
