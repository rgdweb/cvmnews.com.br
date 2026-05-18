---
Task ID: 1
Agent: main
Task: Verificar e corrigir arquivos PHP no servidor cPanel + diagnosticar pronúncia "exatamente"

Work Log:
- Leu os 3 arquivos PHP locais (generate-direct.php, generate.php, generate-omnivoice.php)
- Confirmou que todos têm fixPortuguesePronunciation() com dicionário completo + funções auxiliares (cleanText, stripSSML, CURLOPT_ENCODING)
- Acessou cPanel (sorteiomax.com.br:2083) com credenciais marci955/Rgdweb@2637
- Navegou até /public_html/omnivoice/ no File Manager
- Abriu generate-direct.php no editor do cPanel e confirmou: **FUNÇÃO NÃO EXISTIA NO SERVIDOR**
- Este era o problema raiz: os arquivos que o usuário subiu anteriormente eram versões ANTIGAS sem o dicionário de pronúncia
- Criou ZIP com 14 arquivos PHP corrigidos de /home/z/my-project/php-server/
- Subiu ZIP via cPanel File Manager → Extrair
- Confirmou extração com sucesso (tamanhos aumentaram: generate-direct 23.8KB→24.94KB, generate.php 24.92KB→26.05KB, generate-omnivoice.php 26.94KB→28.07KB)
- Criou check_pronuncia.php para verificar todos os 3 arquivos remotamente
- Resultado: TODOS os 3 arquivos têm TODAS as correções:
  - fixPortuguesePronunciation(): ✅
  - Dicionário "ezatamente": ✅
  - Chamada no pipeline fixPortuguesePronunciation($texto): ✅
  - Dicionário palavras corrompidas (ekssatamente→ezatamente): ✅
  - cleanText(): ✅
  - stripSSML(): ✅
  - CURLOPT_ENCODING => '': ✅
- Limpou arquivos temporários (check_pronuncia.php, .zip)

Stage Summary:
- PROBLEMA DESCOBERTO: Os arquivos no servidor NÃO tinham a função fixPortuguesePronunciation(). Os arquivos que o usuário subiu antes eram versões antigas.
- CORREÇÃO: Todos os 3 PHP foram atualizados com versões locais corretas
- O "exatamente" falando "ekssatamente" era porque o dicionário de pronúncia simplesmente NÃO EXISTIA no servidor
- Agora com os arquivos corretos, "exatamente" será substituído por "ezatamente" ANTES de enviar ao TTS
