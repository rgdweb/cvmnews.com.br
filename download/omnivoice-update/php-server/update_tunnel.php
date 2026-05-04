<?php
/*
 * OmniVoice TTS - Recebe a nova URL do tunnel e atualiza o config.php
 * Chamado automaticamente pelo start_tunnel.ps1 quando o cloudflared inicia
 * Aceita POST (JSON body) ou GET (query string) para max compatibilidade
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Pega a URL do tunnel - aceita POST body ou GET query param
$tunnelUrl = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $tunnelUrl = trim($input['tunnelUrl'] ?? '');
}

// Se veio vazio por POST, tenta via GET
if (empty($tunnelUrl)) {
    $tunnelUrl = trim($_GET['tunnelUrl'] ?? '');
}

// Se ainda vazio, tenta via POST form
if (empty($tunnelUrl)) {
    $tunnelUrl = trim($_POST['tunnelUrl'] ?? '');
}

if (empty($tunnelUrl)) {
    http_response_code(400);
    echo json_encode(['error' => 'tunnelUrl obrigatorio. Use POST {"tunnelUrl":"..."} ou GET ?tunnelUrl=...']);
    exit;
}

// Valida se e URL do cloudflared
if (strpos($tunnelUrl, 'trycloudflare.com') === false) {
    http_response_code(400);
    echo json_encode(['error' => 'URL invalida - esperado trycloudflare.com']);
    exit;
}

// Atualiza o config.php em formato INI
$configFile = __DIR__ . '/config.php';

$configContent = "; Configuracao OmniVoice TTS\n";
$configContent .= "; Atualizado automaticamente pelo start_tunnel.ps1\n";
$configContent .= "tunnel_url = \"{$tunnelUrl}\"\n";
$configContent .= "updated_at = \"" . date('Y-m-d H:i:s') . "\"\n";

file_put_contents($configFile, $configContent);

echo json_encode([
    'status' => 'ok',
    'tunnelUrl' => $tunnelUrl,
    'updated_at' => date('Y-m-d H:i:s')
]);
