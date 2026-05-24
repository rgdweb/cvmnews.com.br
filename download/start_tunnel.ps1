$port = 7860
$auth = "vozpro_tunnel_2024"
$serverUpdate = "https://sorteiomax.com.br/omnivoice/update_tunnel.php"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  VozPro - Tunnel Automatico" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Aguardar OmniVoice ficar pronto (ate 120 segundos)
Write-Host "[1/2] Aguardando OmniVoice na porta $port..." -ForegroundColor Yellow
$esperado = 0
$pronto = $false

for ($i = 0; $i -lt 24; $i++) {
    try {
        $null = Invoke-WebRequest -Uri "http://localhost:$port/" -TimeoutSec 5 -UseBasicParsing -ErrorAction Stop
        $pronto = $true
        break
    } catch {
        # Ainda nao esta pronto, aguardar
    }
    $esperado += 5
    Write-Host "      Aguardando... ${esperado}s" -ForegroundColor DarkGray
    Start-Sleep -Seconds 5
}

if (-not $pronto) {
    Write-Host "[AVISO] OmniVoice nao respondeu apos 120s, tentando tunnel mesmo assim..." -ForegroundColor Yellow
} else {
    Write-Host "[OK] OmniVoice respondendo! (${esperado}s)" -ForegroundColor Green
}

Write-Host ""
Write-Host "[2/2] Abrindo tunnel..." -ForegroundColor Yellow
Write-Host ""

# Verificar se cloudflared existe
$cfPath = $null
$cfPaths = @(
    "C:\Users\Administrador\AppData\Local\Microsoft\WinGet\Links\cloudflared.exe",
    "$env:LOCALAPPDATA\Microsoft\WinGet\Links\cloudflared.exe",
    "C:\Program Files\cloudflared\cloudflared.exe",
    "$env:ProgramFiles\cloudflared\cloudflared.exe"
)

foreach ($p in $cfPaths) {
    if (Test-Path $p) {
        $cfPath = $p
        break
    }
}

# Tentar achar no PATH
if (-not $cfPath) {
    $cfFromPath = Get-Command cloudflared -ErrorAction SilentlyContinue
    if ($cfFromPath) {
        $cfPath = $cfFromPath.Source
    }
}

if ($cfPath) {
    # ========= USAR CLOUDFLARED =========
    Write-Host "[OK] cloudflared encontrado: $cfPath" -ForegroundColor Green
    Write-Host ""
    Write-Host "Iniciando cloudflared tunnel..." -ForegroundColor Yellow

    $cfProcess = Start-Process -FilePath $cfPath -ArgumentList "tunnel", "--url", "http://localhost:$port" -NoNewWindow -PassThru -RedirectStandardOutput "$env:TEMP\cf_output.txt" -RedirectStandardError "$env:TEMP\cf_error.txt"

    # Aguardar URL do cloudflared
    $cfUrl = $null
    for ($i = 0; $i -lt 60; $i++) {
        Start-Sleep -Milliseconds 1000
        $errContent = ""
        if (Test-Path "$env:TEMP\cf_error.txt") {
            $errContent = Get-Content "$env:TEMP\cf_error.txt" -Raw -ErrorAction SilentlyContinue
        }
        $outContent = ""
        if (Test-Path "$env:TEMP\cf_output.txt") {
            $outContent = Get-Content "$env:TEMP\cf_output.txt" -Raw -ErrorAction SilentlyContinue
        }
        $allContent = "$outContent $errContent"

        if ($allContent -match "(https://[a-z0-9\-]+\.trycloudflare\.com)") {
            $cfUrl = $Matches[1]
            break
        }
    }

    if ($cfUrl) {
        Write-Host "========================================" -ForegroundColor Green
        Write-Host "  URL: $cfUrl" -ForegroundColor Green
        Write-Host "========================================" -ForegroundColor Green
        Write-Host ""

        # Registrar URL no servidor PHP (POST JSON - mais confiavel que GET)
        $registered = $false
        for ($tentativa = 1; $tentativa -le 3; $tentativa++) {
            try {
                $body = @{ tunnelUrl = $cfUrl } | ConvertTo-Json
                $resp = Invoke-RestMethod -Uri $serverUpdate -Method POST -Body $body -ContentType "application/json" -TimeoutSec 15
                if ($resp.status -eq 'ok') {
                    Write-Host "[OK] Servidor atualizado automaticamente!" -ForegroundColor Green
                    $registered = $true
                    break
                } else {
                    Write-Host "[ERRO] Tentativa $tentativa/3 : $($resp.error)" -ForegroundColor Red
                }
            } catch {
                Write-Host "[AVISO] Tentativa $tentativa/3 falhou: $($_.Exception.Message)" -ForegroundColor Yellow
            }
            if ($tentativa -lt 3) { Start-Sleep -Seconds 3 }
        }
        if (-not $registered) {
            Write-Host "[AVISO] Nao conseguiu atualizar servidor PHP apos 3 tentativas." -ForegroundColor Yellow
            Write-Host "        O tunnel funciona localmente, mas a Vercel nao vai encontrar a URL." -ForegroundColor Yellow
        }

        Write-Host ""
        Write-Host "Tunnel ativo! Nao feche esta janela." -ForegroundColor Yellow
        Write-Host "Pressione Ctrl+C para parar." -ForegroundColor DarkGray
        Write-Host ""

        try {
            $cfProcess.WaitForExit()
        } catch {
            Start-Sleep -Seconds 999999
        }
    } else {
        Write-Host "[ERRO] Cloudflared nao gerou URL" -ForegroundColor Red
        Write-Host "Tentando localtunnel como fallback..." -ForegroundColor Yellow

        # Fallback para localtunnel
        . do_fallback_localtunnel
    }

} else {
    # ========= USAR LOCALTUNNEL (FALLBACK) =========
    Write-Host "[INFO] cloudflared nao encontrado, usando localtunnel..." -ForegroundColor Yellow
    . do_fallback_localtunnel
}

function do_fallback_localtunnel {
    $outputFile = "$env:TEMP\lt_output.txt"
    Remove-Item $outputFile -Force -ErrorAction SilentlyContinue

    $job = Start-Job -ScriptBlock {
        param($p)
        cmd /c "npx localtunnel --port $p 2>&1" | Out-File -FilePath $using:outputFile -Encoding ascii
    } -ArgumentList $port

    $url = $null
    for ($i = 0; $i -lt 60; $i++) {
        Start-Sleep -Milliseconds 500
        if (Test-Path $outputFile) {
            $content = Get-Content $outputFile -Raw -ErrorAction SilentlyContinue
            if ($content -match "your url is: (https://[a-z0-9\-]+\.loca\.lt)") {
                $url = $Matches[1]
                break
            }
        }
    }

    if ($url) {
        Write-Host "========================================" -ForegroundColor Green
        Write-Host "  URL: $url" -ForegroundColor Green
        Write-Host "========================================" -ForegroundColor Green
        Write-Host ""

        # Registrar URL no servidor PHP (POST JSON)
        try {
            $body = @{ tunnelUrl = $url } | ConvertTo-Json
            $resp = Invoke-RestMethod -Uri $serverUpdate -Method POST -Body $body -ContentType "application/json" -TimeoutSec 15
            if ($resp.status -eq 'ok') {
                Write-Host "[OK] Servidor atualizado automaticamente!" -ForegroundColor Green
            } else {
                Write-Host "[ERRO] $($resp.error)" -ForegroundColor Red
            }
        } catch {
            Write-Host "[AVISO] Nao conseguiu atualizar servidor PHP: $($_.Exception.Message)" -ForegroundColor Yellow
        }

        Write-Host ""
        Write-Host "Tunnel ativo! Nao feche esta janela." -ForegroundColor Yellow
        Write-Host "

        try {
            Wait-Job $job | Out-Null
        } catch {
            Start-Sleep -Seconds 999999
        }
    } else {
        Write-Host "[ERRO] Nao conseguiu obter URL de nenhum tunnel!" -ForegroundColor Red
        Write-Host "Verifique sua internet e tente novamente." -ForegroundColor Yellow
        Start-Sleep -Seconds 10
    }
}
