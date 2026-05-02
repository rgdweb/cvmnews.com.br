# start_tunnel.ps1 - Abre localtunnel e atualiza o servidor automaticamente
# Requer: Node.js instalado (ja tem)

$port = 7860
$auth = "vozpro_tunnel_2024"
$serverUpdate = "https://sorteiomax.com.br/omnivoice/update_tunnel.php"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  OmniVoice - Tunnel Automatico" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verificar se OmniVoice esta rodando
Write-Host "[1/2] Verificando OmniVoice na porta $port..." -ForegroundColor Yellow
try {
    $null = Invoke-WebRequest -Uri "http://localhost:$port/" -TimeoutSec 5 -ErrorAction Stop
    Write-Host "[OK] OmniVoice respondendo!" -ForegroundColor Green
} catch {
    Write-Host "[ERRO] OmniVoice NAO esta rodando na porta $port!" -ForegroundColor Red
    Write-Host "Execute o iniciar.bat primeiro." -ForegroundColor Red
    Read-Host "Pressione Enter para sair"
    exit 1
}

Write-Host ""
Write-Host "[2/2] Abrindo localtunnel..." -ForegroundColor Yellow

# Iniciar localtunnel via npx e capturar a URL
$proc = Start-Process -FilePath "npx" -ArgumentList "localtunnel --port $port" -NoNewWindow -PassThru -RedirectStandardOutput "$env:TEMP\lt_output.txt" -RedirectStandardError "$env:TEMP\lt_error.txt"

# Aguardar ate 30 segundos pela URL
$url = $null
for ($i = 0; $i -lt 60; $i++) {
    Start-Sleep -Milliseconds 500
    if (Test-Path "$env:TEMP\lt_output.txt") {
        $content = Get-Content "$env:TEMP\lt_output.txt" -Raw -ErrorAction SilentlyContinue
        if ($content -match "your url is: (https://[a-z0-9\-]+\.loca\.lt)") {
            $url = $Matches[1]
            break
        }
    }
}

if ($url) {
    Write-Host ""
    Write-Host "========================================" -ForegroundColor Green
    Write-Host "  URL: $url" -ForegroundColor Green
    Write-Host "========================================" -ForegroundColor Green
    Write-Host ""

    # Atualizar servidor
    try {
        $updateUrl = "$serverUpdate?auth=$auth&url=$([System.Web.HttpUtility]::UrlEncode($url))"
        $null = Invoke-WebRequest -Uri $updateUrl -TimeoutSec 15 -ErrorAction Stop
        Write-Host "[OK] Servidor atualizado automaticamente!" -ForegroundColor Green
    } catch {
        Write-Host "[ERRO] Nao foi possivel atualizar o servidor: $($_.Exception.Message)" -ForegroundColor Red
    }

    Write-Host ""
    Write-Host "Tunel ativo! Nao feche esta janela." -ForegroundColor Yellow
    Write-Host "Pressione Ctrl+C para parar." -ForegroundColor DarkGray
    Write-Host ""

    # Manter rodando - mostrar saida do localtunnel
    try {
        $proc.WaitForExit()
    } catch {
        Start-Sleep -Seconds 999999
    }
} else {
    Write-Host "[ERRO] Nao conseguiu obter URL do localtunnel" -ForegroundColor Red
    $err = Get-Content "$env:TEMP\lt_error.txt" -Raw -ErrorAction SilentlyContinue
    if ($err) { Write-Host "Erro: $err" -ForegroundColor Red }
    Read-Host "Pressione Enter para sair"
}
