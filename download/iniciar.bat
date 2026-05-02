@echo off
title OmniVoice GPU Server
echo ============================================
echo   OmniVoice TTS - Servidor GPU Local
echo ============================================
echo.

REM Limpar memoria CUDA antes de iniciar
echo [0/3] Configurando ambiente GPU...
set PYTORCH_CUDA_ALLOC_CONF=expandable_segments:True
echo      PYTORCH_CUDA_ALLOC_CONF=expandable_segments:True

REM Iniciar OmniVoice Demo na GPU
echo [1/3] Iniciando OmniVoice Demo na GPU (porta 7860)...
start "OmniVoice GPU" cmd /k "set PYTORCH_CUDA_ALLOC_CONF=expandable_segments:True && omnivoice-demo --ip 0.0.0.0 --port 7860"

echo      Aguardando OmniVoice iniciar (15 segundos)...
timeout /t 15 /nobreak >nul

REM Iniciar localtunnel (exposicao publica via SSE compativel)
echo [2/3] Iniciando localtunnel (SSE compativel)...
start "Localtunnel" cmd /k "npx localtunnel --port 7860"

echo      Aguardando localtunnel (10 segundos)...
timeout /t 10 /nobreak >nul

echo.
echo ============================================
echo   Servidor pronto!
echo ============================================
echo.
echo   OmniVoice: http://localhost:7860
echo.
echo   IMPORTANTE:
echo   - A janela do Localtunnel vai mostrar a URL publica (ex: https://xxxx.loca.lt)
echo   - Copie essa URL e atualize config.php no servidor
echo   - Audio de referencia sera automaticamente trimado para 10s
echo   - Feche todas as janelas para parar o servidor
echo.
pause
