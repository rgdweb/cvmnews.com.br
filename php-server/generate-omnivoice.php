<?php
// generate-omnivoice.php - Geracao de voz TTS via OmniVoice (PHP direto do browser)
// Suporta 3 modos: clone (_clone_fn), design (_design_fn), auto (_design_fn com Auto)
// Usa o mesmo padrao HMAC do generate.php
// Bypassa o Vercel completamente - zero gasto de serverless
//
// CORRECOES (15/05/2026):
// - Adicionada cleanText() para remover caracteres de controle invisiveis
// - memory_limit 256M -> 512M (base64 de audio longo precisa mais memoria)
// - SSE timeout 300s -> 600s (textos longos nao estouram)
// - po (postprocess) mantido true (limpa artefatos/estalos do audio gerado)
// - CURLOPT_ENCODING => '' adicionado no fetch da tunnel URL
// - Timeout submit job 60s -> 90s
// - Timeout download audio 120s -> 180s
//
// CORRECOES (15/05/2026 v2):
// - CRITICO: Adicionado trim do audio de referencia para 12s (evita CUDA OOM na RTX 3060 12GB)
//   Sem trim, audio de ref longo causava corte no final, travadas e audio corrompido
// - REMOVIDO normalizePronunciation() — dicionario criava palavras inexistentes
// - Adicionado mb_strtolower() para converter texto a minusculo antes do TTS
// - Adicionado CHUNKING completo: textos longos sao divididos em pedacos (max 400 chars)
//   Cada chunk e gerado separadamente pelo TTS, depois os audios sao concatenados
//   Resolve: audio cortado no final, pontuacao ignorada, textos longos truncados
// - Adicionado concatenateWavFiles() para juntar audios WAV sem depender de ffmpeg
// - Upload de audio de referencia feito 1 vez e reusado em todos os chunks

// VERSAO: 20260515-v5-fix (substr bug fix + opcache auto-invalidate)
// Auto-invalidate OPcache em toda requisicao (previne codigo antigo em cache)
if (function_exists('opcache_invalidate')) { @opcache_invalidate(__FILE__, true); }
// ===== CAPTURA DE ERROS FATAIS =====
// Se PHP crashar (fatal error, out of memory, etc), retorna JSON ao inves de pagina em branco 500
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        http_response_code(500);
        echo json_encode([
            'erro' => 'Erro fatal do PHP: ' . $error['message'],
            'file' => basename($error['file']),
            'line' => $error['line'],
            'php_fatal' => true
        ]);
        exit;
    }
});

set_time_limit(0);
ini_set('max_input_time', 0);
ini_set('memory_limit', '512M');

require_once __DIR__ . '/config.php';

// CORS
header_remove('Access-Control-Allow-Origin');
header_remove('Access-Control-Allow-Methods');
header_remove('Access-Control-Allow-Headers');
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Generate-Token');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido']);
    exit;
}

// ===================== VALIDACAO HMAC TOKEN =====================
$token = $_SERVER['HTTP_X_GENERATE_TOKEN'] ?? '';
if (empty($token)) {
    http_response_code(401);
    echo json_encode(['erro' => 'Token de geracao necessario']);
    exit;
}

$parts = explode('.', $token);
if (count($parts) !== 2) {
    http_response_code(401);
    echo json_encode(['erro' => 'Token invalido (formato)']);
    exit;
}

$timestamp = (int)$parts[0];
$receivedHmac = $parts[1];

if (time() - $timestamp > 1800) {
    http_response_code(401);
    echo json_encode(['erro' => 'Token expirado, tente novamente']);
    exit;
}

if ($timestamp > time() + 60) {
    http_response_code(401);
    echo json_encode(['erro' => 'Token invalido']);
    exit;
}

$expectedHmac = hash_hmac('sha256', (string)$timestamp, API_KEY);
if (!hash_equals($expectedHmac, $receivedHmac)) {
    http_response_code(401);
    echo json_encode(['erro' => 'Token invalido (assinatura)']);
    exit;
}

// ===================== DEBUG LOGGER =====================
$debugSteps = [];
$debugStart = microtime(true);

function debugLog($step, $status, $detail = '') {
    global $debugSteps, $debugStart;
    $debugSteps[] = [
        'time' => date('H:i:s'),
        'step' => $step,
        'status' => $status,
        'detail' => $detail,
        'duration' => round((microtime(true) - $debugStart) * 1000)
    ];
}

function debugResult() {
    global $debugSteps, $debugStart;
    return [
        'totalDuration' => round((microtime(true) - $debugStart) * 1000),
        'steps' => $debugSteps
    ];
}

function returnError($msg, $code = 500) {
    http_response_code($code);
    echo json_encode([
        'erro' => $msg,
        'debug' => debugResult()
    ]);
    exit;
}

// ===================== STRIP SSML (defesa) =====================
// Se o frontend enviar SSML, remove tudo. TTS nao entende tags.
function stripSSML($text) {
    if (!is_string($text)) return '';
    if (!preg_match('/<[a-z][^>]*>/i', $text)) return $text;
    $r = preg_replace('/<[^>]+>/', '', $text);
    $r = html_entity_decode($r, ENT_QUOTES | ENT_XML1, 'UTF-8');
    return trim(preg_replace('/\s+/', ' ', $r));
}

// ===================== LIMPAR TEXTO (defesa extra) =====================
// Remove caracteres de controle invisiveis que podem causar garbling
function cleanText($text) {
    if (!is_string($text)) return '';
    $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
    $text = str_replace(["\r\n", "\r"], "\n", $text);
    $text = preg_replace('/\n{3,}/', "\n\n", $text);
    return trim($text);
}

// ===================== TRIMAR AUDIO DE REFERENCIA =====================
// CRITICO: Limitar audio de ref para evitar CUDA OOM na RTX 3060 12GB.
// Sem este trim, audio de referencia longo (>12s) pode causar:
// - Audio cortado no final da geracao
// - Travadas e silencios no meio do audio
// - Audio corrompido com artefatos
// Usa trim_audio.py existente (sem depender de ffmpeg)
define('MAX_REF_AUDIO_SECONDS', 12);

function trimAudioToMaxSeconds($filePath, $maxSeconds = 12) {
    $trimScript = __DIR__ . '/trim_audio.py';
    if (!file_exists($trimScript)) {
        debugLog('Trim ref audio', 'warn', 'trim_audio.py nao encontrado, usando original');
        return false;
    }
    // Verificar se shell_exec esta disponivel (Hostgator pode desabilitar)
    if (!function_exists('shell_exec') || in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
        debugLog('Trim ref audio', 'warn', 'shell_exec desabilitado, usando original');
        return false;
    }
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    $trimmedFile = tempnam(sys_get_temp_dir(), 'vp_trim_') . '.' . $ext;

    $cmd = 'python3 ' . escapeshellarg($trimScript) . ' '
         . escapeshellarg($filePath) . ' '
         . escapeshellarg($trimmedFile) . ' '
         . escapeshellarg((string)$maxSeconds) . ' 2>&1';

    $output = @shell_exec($cmd);
    $trimOk = (trim($output ?? '') === 'OK');

    if ($trimOk && file_exists($trimmedFile) && filesize($trimmedFile) > 0) {
        return $trimmedFile;
    }
    // Falha no trim, apagar arquivo temporario e retornar false
    if (file_exists($trimmedFile)) unlink($trimmedFile);
    return false;
}

// normalizePronunciation REMOVIDO (15/05/2026 v3):
// O dicionario fonetico anterior criava palavras que nao existem
// (ex: sucesso -> suceSSo, profissional -> profeSSional), piorando
// a pronuncia. O modelo GPT-SoVITS ja fala portugues nativamente.
// A solucao correta e converter o texto para minusculo (mb_strtolower)
// aplicado na pipeline abaixo, antes do envio ao TTS.

// ===================== SPLIT DE TEXTO LONGO =====================
// Divide texto em chunks por pontuacao para evitar que TTS corte audio no final.
// TTS com texto muito longo pode gerar audio truncado por limite de tokens.
function splitTextIntoChunks($text, $maxChars = 400) {
    if (mb_strlen($text) <= $maxChars) {
        return [$text];
    }

    $chunks = [];
    // Dividir por pontuacao forte (., !, ?, ...) mantendo a pontuacao no chunk
    $sentences = preg_split('/(?<=[.!?…])\s+/', $text);
    $current = '';

    foreach ($sentences as $sentence) {
        if (mb_strlen($current . ' ' . $sentence) > $maxChars && !empty($current)) {
            $chunks[] = trim($current);
            $current = $sentence;
        } else {
            $current = ($current ? $current . ' ' : '') . $sentence;
        }
    }

    if (!empty(trim($current))) {
        $chunks[] = trim($current);
    }

    // Fallback: se nao dividiu por pontuacao (texto sem pontuacao), cortar por virgula
    if (count($chunks) <= 1 && mb_strlen($text) > $maxChars) {
        $chunks = [];
        $phrases = preg_split('/(?<=,|;|:)\s+/', $text);
        $current = '';
        foreach ($phrases as $phrase) {
            if (mb_strlen($current . ' ' . $phrase) > $maxChars && !empty($current)) {
                $chunks[] = trim($current);
                $current = $phrase;
            } else {
                $current = ($current ? $current . ' ' : '') . $phrase;
            }
        }
        if (!empty(trim($current))) {
            $chunks[] = trim($current);
        }
    }

    // Ultimo fallback: cortar por palavras
    if (count($chunks) <= 1 && mb_strlen($text) > $maxChars) {
        $chunks = [];
        $words = explode(' ', $text);
        $current = '';
        foreach ($words as $word) {
            if (mb_strlen($current . ' ' . $word) > $maxChars && !empty($current)) {
                $chunks[] = trim($current);
                $current = $word;
            } else {
                $current = ($current ? $current . ' ' : '') . $word;
            }
        }
        if (!empty(trim($current))) {
            $chunks[] = trim($current);
        }
    }

    return $chunks;
}

// ===================== CONCATENAR AUDIO =====================
// Junta multiplos arquivos WAV em um so (sem ffmpeg, puro PHP)
function concatenateWavFiles($wavFiles) {
    if (count($wavFiles) === 0) return null;
    if (count($wavFiles) === 1) return $wavFiles[0];

    // Ler primeiro arquivo para extrair header WAV
    $firstData = file_get_contents($wavFiles[0]);

    // Verificar se e WAV valido (deve comecar com RIFF)
    if (substr($firstData, 0, 4) !== 'RIFF') {
        return null;
    }

    // Extrair parametros do header WAV (44 bytes para PCM padrão)
    $numChannels = unpack('v', substr($firstData, 22, 2))[1];
    $sampleRate = unpack('V', substr($firstData, 24, 4))[1];
    $bitsPerSample = unpack('v', substr($firstData, 34, 2))[1];
    $byteRate = unpack('V', substr($firstData, 28, 4))[1];
    $blockAlign = unpack('v', substr($firstData, 32, 2))[1];

    // Extrair dados PCM do primeiro arquivo (pular header de 44 bytes)
    $pcmData = substr($firstData, 44);

    // Adicionar dados PCM dos demais arquivos
    for ($i = 1; $i < count($wavFiles); $i++) {
        $data = file_get_contents($wavFiles[$i]);
        if (strlen($data) > 44 && substr($data, 0, 4) === 'RIFF') {
            $pcmData .= substr($data, 44);
        }
    }

    // Montar novo WAV com header atualizado
    $dataSize = strlen($pcmData);
    $fileSize = 36 + $dataSize;
    $newHeader = pack('A4VA4A4VvvVVvvA4V',
        'RIFF', $fileSize, 'WAVE', 'fmt ', 16,
        1, // PCM
        $numChannels, $sampleRate, $byteRate,
        $blockAlign, $bitsPerSample, 'data', $dataSize
    );

    $outputFile = tempnam(sys_get_temp_dir(), 'vp_concat_') . '.wav';
    file_put_contents($outputFile, $newHeader . $pcmData);

    return $outputFile;
}

// ===================== BAIXAR AUDIO GERADO =====================
function downloadGeneratedAudio($audioUrl) {
    debugLog('Download chunk audio', 'info', 'baixando...');
    $tempFile = tempnam(sys_get_temp_dir(), 'vp_ov_chunk_');
    $ext = strtolower(pathinfo($audioUrl, PATHINFO_EXTENSION));
    if ($ext) $tempFile .= '.' . $ext;

    $ch = curl_init($audioUrl);
    $fp = fopen($tempFile, 'w');
    curl_setopt_array($ch, [
        CURLOPT_FILE => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 180,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $dlOk = curl_exec($ch);
    $dlCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);

    if (!$dlOk || $dlCode != 200 || filesize($tempFile) == 0) {
        if (file_exists($tempFile)) unlink($tempFile);
        return null;
    }

    debugLog('Download chunk audio', 'ok', round(filesize($tempFile) / 1024) . 'KB');
    return $tempFile;
}

// ===================== LER INPUT JSON =====================
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    returnError('JSON invalido ou vazio', 400);
}

$texto = $input['text'] ?? '';
$mode = $input['mode'] ?? 'clone';       // clone | design | auto
$idioma = $input['language'] ?? 'Auto';
$refAudioUrl = $input['referenceAudioUrl'] ?? '';
$refAudioName = $input['referenceAudioName'] ?? 'ref_audio.wav';
$instruct = $input['instruct'] ?? '';
$speed = $input['speed'] ?? 1.0;
$numStep = $input['numStep'] ?? 32;

// Voice Design params (usados no _design_fn)
$gender = $input['gender'] ?? 'Auto';
$age = $input['age'] ?? 'Auto';
$pitch = $input['pitch'] ?? 'Auto';
$style = $input['style'] ?? 'Auto';
$accent = $input['accent'] ?? 'Auto';

// ===================== DEFESA: STRIP SSML + CLEAN + MINUSCULO =====================
// 1. Remove tags SSML (TTS nao entende SSML)
// 2. Remove caracteres de controle invisiveis
// 3. Converte para minusculo — o TTS pronuncia melhor texto em minusculo.
//    Maiusculas no meio da palavra causam enfase errada e gagueira.
$texto = stripSSML($texto);
$texto = cleanText($texto);
$texto = mb_strtolower($texto, 'UTF-8');

debugLog('Input', 'info', "modo: $mode | texto: " . mb_substr($texto, 0, 50) . " | lang: $idioma | steps: $numStep");

if (empty(trim($texto))) {
    returnError('Texto e obrigatorio', 400);
}

if ($mode === 'clone' && empty($refAudioUrl)) {
    returnError('Audio de referencia necessario no modo clone', 400);
}

// ===================== OBTER TUNNEL URL =====================
debugLog('Tunnel', 'info', 'Descobrindo URL do tunnel...');

$tunnelUrl = null;
// Primeiro tenta a constante TUNNEL_URL do config (atualizada pelo start_tunnel.ps1)
if (defined('TUNNEL_URL') && !empty(TUNNEL_URL)) {
    $tunnelUrl = TUNNEL_URL;
    debugLog('Tunnel', 'info', 'Usando TUNNEL_URL do config');
} else {
    // Fallback: tenta via get_tunnel.php
    $tunnelCh = curl_init(BASE_URL . '/get_tunnel.php');
    curl_setopt_array($tunnelCh, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $tunnelResp = curl_exec($tunnelCh);
    $tunnelCode = curl_getinfo($tunnelCh, CURLINFO_HTTP_CODE);
    curl_close($tunnelCh);

    if ($tunnelCode == 200 && $tunnelResp) {
        $tunnelData = json_decode($tunnelResp, true);
        if (($tunnelData['status'] ?? '') === 'online' && !empty($tunnelData['tunnelUrl'])) {
            $tunnelUrl = $tunnelData['tunnelUrl'];
        }
    }
}

if (!$tunnelUrl) {
    // Fallback final para HF_SPACE_URL
    $tunnelUrl = defined('HF_SPACE_URL') ? HF_SPACE_URL : '';
}

if (empty($tunnelUrl)) {
    returnError('Servidor OmniVoice offline - tunnel nao disponivel', 503);
}

debugLog('Tunnel', 'ok', mb_substr($tunnelUrl, 0, 60) . '...');

// ===================== FUNCOES =====================

function downloadRefAudio($url, $name) {
    debugLog('Download ref audio', 'info', 'de: ' . mb_substr($url, 0, 80));
    $tempFile = tempnam(sys_get_temp_dir(), 'vp_ov_') . '.' . pathinfo($name, PATHINFO_EXTENSION);

    $ch = curl_init($url);
    $fp = fopen($tempFile, 'w');
    curl_setopt_array($ch, [
        CURLOPT_FILE => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $dlOk = curl_exec($ch);
    $dlHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);

    if (!$dlOk || $dlHttpCode != 200 || filesize($tempFile) == 0) {
        debugLog('Download ref audio', 'error', "HTTP $dlHttpCode");
        if (file_exists($tempFile)) unlink($tempFile);
        return null;
    }

    debugLog('Download ref audio', 'ok', round(filesize($tempFile) / 1024) . 'KB');
    return $tempFile;
}

function uploadToGradio($filePath, $fileName, $baseUrl) {
    debugLog('Upload Gradio', 'info', 'enviando...');

    $ch = curl_init($baseUrl . '/gradio_api/upload');
    $cfile = new CURLFile($filePath, mime_content_type($filePath), $fileName);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ['files' => $cfile],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code == 200 && $resp) {
        $data = json_decode($resp, true);
        if (is_array($data) && count($data) > 0) {
            debugLog('Upload Gradio', 'ok', $data[0]);
            return $data[0];
        }
    }
    debugLog('Upload Gradio', 'error', "HTTP $code");
    return null;
}

function submitJob($baseUrl, $endpoint, $gradioData) {
    debugLog('Submit', 'info', "endpoint: $endpoint");

    $ch = curl_init($baseUrl . '/gradio_api/call/' . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['data' => $gradioData]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 90,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_ENCODING => '',
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code != 200 || !$resp) {
        debugLog('Submit', 'error', "HTTP $code");
        return null;
    }

    $data = json_decode($resp, true);
    $eventId = $data['event_id'] ?? null;

    if ($eventId) {
        debugLog('Submit', 'ok', "event_id: $eventId");
    } else {
        debugLog('Submit', 'error', 'sem event_id');
    }

    return $eventId;
}

function streamResult($baseUrl, $endpoint, $eventId, $timeoutSec = 600) {
    debugLog('SSE Stream', 'info', "Abrindo conexao para $eventId (timeout: {$timeoutSec}s)...");

    $audioUrl = null;
    $error = null;
    $buffer = '';
    $heartbeatCount = 0;
    $startTime = time();

    $ch = curl_init($baseUrl . '/gradio_api/call/' . $endpoint . '/' . $eventId);

    $writeFn = function($ch, $chunk) use (&$buffer, &$audioUrl, &$error, &$heartbeatCount, &$startTime, $timeoutSec) {
        $buffer .= $chunk;

        if (time() - $startTime > $timeoutSec) {
            return -1;
        }

        $blocks = explode("\n\n", $buffer);
        $buffer = array_pop($blocks) ?? '';

        foreach ($blocks as $block) {
            $block = trim($block);
            if (empty($block)) continue;

            $lines = explode("\n", $block);
            $eventType = '';
            $eventData = '';

            foreach ($lines as $line) {
                if (strpos($line, 'event: ') === 0) {
                    $eventType = trim(substr($line, 7));
                }
                if (strpos($line, 'data: ') === 0) {
                    $eventData = trim(substr($line, 6));
                }
            }

            if ($eventType === 'complete' && !empty($eventData)) {
                debugLog('SSE Stream', 'ok', 'Evento COMPLETE recebido!');
                $resultData = json_decode($eventData, true);
                // Gradio retorna [audio_output(FileData), status_text]
                if (is_array($resultData) && count($resultData) >= 2) {
                    $output = $resultData[0];
                    if (isset($output['url'])) {
                        $audioUrl = $output['url'];
                    } elseif (isset($output['path'])) {
                        $audioUrl = $baseUrl . '/gradio_api/file=' . $output['path'];
                    }
                }
                if ($audioUrl) {
                    debugLog('SSE Stream', 'ok', 'Audio: ' . mb_substr($audioUrl, 0, 80));
                } else {
                    $error = 'Sem URL no output';
                }
                return -1;
            }

            if ($eventType === 'error') {
                debugLog('SSE Stream', 'error', mb_substr($eventData ?: 'vazio', 0, 300));
                $error = $eventData ?: 'Erro na geracao';
                return -1;
            }

            if ($eventType === 'heartbeat') {
                $heartbeatCount++;
                if ($heartbeatCount <= 2 || $heartbeatCount % 15 === 0) {
                    debugLog('SSE Stream', 'info', "Heartbeat #$heartbeatCount");
                }
            }
        }

        return strlen($chunk);
    };

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_TIMEOUT => $timeoutSec,
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_ENCODING => '',
        CURLOPT_HTTPHEADER => [
            'Accept: text/event-stream',
            'Cache-Control: no-cache',
            'Connection: keep-alive',
            'X-Accel-Buffering: no',
            'Accept-Encoding: identity',
        ],
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_WRITEFUNCTION => $writeFn,
    ]);

    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($audioUrl) {
        return ['audioUrl' => $audioUrl, 'error' => null];
    }
    if ($error) {
        return ['audioUrl' => null, 'error' => $error];
    }
    if ($httpCode == 404) {
        return ['audioUrl' => null, 'error' => '404'];
    }
    if (!empty($curlError)) {
        return ['audioUrl' => null, 'error' => 'connection_lost: ' . $curlError];
    }
    if (time() - $startTime >= $timeoutSec) {
        return ['audioUrl' => null, 'error' => 'timeout'];
    }
    return ['audioUrl' => null, 'error' => 'stream_ended'];
}

// ===================== MONTAR DADOS E GERAR =====================

$tempRefFile = null;
$endpoint = '';
$gradioData = [];

if ($mode === 'clone') {
    // ===== MODO CLONE: _clone_fn =====
    $endpoint = '_clone_fn';

    // Baixar audio de referencia
    if (!empty($refAudioUrl)) {
        $tempRefFile = downloadRefAudio($refAudioUrl, $refAudioName);
        if (!$tempRefFile) {
            returnError('Falha ao baixar audio de referencia', 400);
        }

        // ===== TRIMAR AUDIO PARA EVITAR CUDA OOM =====
        // CRITICO: Sem trim, audio de ref longo causa:
        // - Audio cortado no final das frases
        // - CUDA OOM na RTX 3060 12GB
        // - Audio corrompido, travadas, silencios
        $trimmedFile = trimAudioToMaxSeconds($tempRefFile, MAX_REF_AUDIO_SECONDS);
        if ($trimmedFile && $trimmedFile !== $tempRefFile) {
            if (file_exists($tempRefFile)) unlink($tempRefFile);
            $tempRefFile = $trimmedFile;
            debugLog('Trim ref audio', 'ok', round(filesize($tempRefFile) / 1024) . 'KB (max ' . MAX_REF_AUDIO_SECONDS . 's)');
        } elseif ($trimmedFile === false) {
            debugLog('Trim ref audio', 'warn', 'Falha no trim, usando original (pode causar OOM)');
        } else {
            debugLog('Trim ref audio', 'ok', 'Audio ja dentro do limite (' . MAX_REF_AUDIO_SECONDS . 's)');
        }
    }

    $gradioData = [
        $texto,                            // text
        $idioma,                           // lang
        [
            'path' => '',
            'orig_name' => $refAudioName,
            'mime_type' => (pathinfo($refAudioName, PATHINFO_EXTENSION) === 'mp3') ? 'audio/mpeg' : 'audio/wav',
            'is_stream' => false,
            'meta' => ['_type' => 'gradio.FileData']
        ],
        '',                                // ref_text (vazio = auto Whisper)
        $instruct ?: null,                 // instruct
        (int)$numStep,                     // ns
        2.0,                               // gs (CFG)
        true,                              // dn (denoise)
        (float)$speed,                     // sp (speed)
        null,                              // du (duration, null = auto)
        true,                              // pp (preprocess)
        true                               // po (postprocess) - limpa estalos/artefatos do audio
    ];

} else {
    // ===== MODO DESIGN / AUTO: _design_fn =====
    $endpoint = '_design_fn';

    // Se modo auto, forcar todos os params como Auto
    if ($mode === 'auto') {
        $gender = 'Auto';
        $age = 'Auto';
        $pitch = 'Auto';
        $style = 'Auto';
        $accent = 'Auto';
    }

    $gradioData = [
        $texto,                            // text
        $idioma,                           // lang
        (int)$numStep,                     // ns
        2.0,                               // gs (CFG)
        true,                              // dn (denoise)
        (float)$speed,                     // sp (speed)
        null,                              // du (duration)
        true,                              // pp (preprocess)
        true,                              // po (postprocess) - limpa estalos/artefatos
        $gender,                           // gender
        $age,                              // age
        $pitch,                            // pitch
        $style,                            // style
        $accent,                           // english accent
        'Auto'                             // chinese dialect
    ];
}

debugLog('Modo', 'info', "endpoint: $endpoint | gender: $gender | pitch: $pitch");

// ===================== CHUNKING: DIVIDIR TEXTO LONGO =====================
// Textos longos sao divididos para evitar que o TTS corte o audio no final.
// Cada chunk e gerado separadamente e os audios sao concatenados depois.

$chunks = splitTextIntoChunks($texto, 400);
$chunkCount = count($chunks);

if ($chunkCount > 1) {
    debugLog('Chunking', 'info', "Texto dividido em $chunkCount partes (" . mb_strlen($texto) . " chars total)");
    for ($ci = 0; $ci < $chunkCount; $ci++) {
        debugLog('Chunk ' . ($ci + 1), 'info', mb_strlen($chunks[$ci]) . " chars: " . mb_substr($chunks[$ci], 0, 60) . "...");
    }
} else {
    debugLog('Chunking', 'ok', 'Texto curto, sem necessidade de dividir (' . mb_strlen($texto) . ' chars)');
}

// ===================== UPLOAD AUDIO DE REFERENCIA (1 vez) =====================
$refPath = null;
if ($mode === 'clone' && $tempRefFile && file_exists($tempRefFile)) {
    $refPath = uploadToGradio($tempRefFile, $refAudioName, $tunnelUrl);
    if (!$refPath) {
        returnError('Falha no upload do audio de referencia', 400);
    }
    $gradioData[2]['path'] = $refPath;
    $gradioData[2]['url'] = $tunnelUrl . '/gradio_api/file=' . $refPath;
    $gradioData[2]['size'] = filesize($tempRefFile);
    debugLog('Upload ref audio', 'ok', 'Enviado para Gradio (reuso em todos os chunks)');
}

// ===================== GERAR AUDIO PARA CADA CHUNK =====================
$chunkAudioFiles = [];
$generationFailed = false;

for ($ci = 0; $ci < $chunkCount; $ci++) {
    $chunkLabel = ($chunkCount > 1) ? " [chunk " . ($ci + 1) . "/$chunkCount]" : '';
    $chunkText = $chunks[$ci];

    debugLog('Geracao' . $chunkLabel, 'info', "Gerando " . mb_strlen($chunkText) . " chars...");

    // Atualizar texto no gradioData para este chunk
    $chunkGradioData = $gradioData;
    $chunkGradioData[0] = $chunkText;

    // Tentar gerar com retry (ate 3 tentativas por chunk)
    $chunkAudioUrl = null;
    $maxChunkRetries = 3;

    for ($attempt = 0; $attempt < $maxChunkRetries && !$chunkAudioUrl; $attempt++) {
        if ($attempt > 0) {
            $waitSec = 3 * $attempt;
            debugLog('Retry' . $chunkLabel, 'warn', "Tentativa " . ($attempt + 1) . "/$maxChunkRetries (${waitSec}s)...");
            sleep($waitSec);
        }

        // Submit job
        $eventId = null;
        for ($s = 0; $s < 3 && !$eventId; $s++) {
            if ($s > 0) {
                sleep(2);
            }
            $eventId = submitJob($tunnelUrl, $endpoint, $chunkGradioData);
        }

        if (!$eventId) {
            continue;
        }

        // Stream resultado
        $result = streamResult($tunnelUrl, $endpoint, $eventId, 600);

        if ($result['audioUrl']) {
            $chunkAudioUrl = $result['audioUrl'];
            if ($attempt > 0) {
                debugLog('Retry' . $chunkLabel, 'ok', "Sucesso na tentativa " . ($attempt + 1));
            }
        }
    }

    if (!$chunkAudioUrl) {
        debugLog('Geracao' . $chunkLabel, 'error', 'Falhou apos todas as tentativas');
        $generationFailed = true;
        break;
    }

    // Baixar audio gerado deste chunk
    $chunkFile = downloadGeneratedAudio($chunkAudioUrl);
    if (!$chunkFile) {
        debugLog('Download' . $chunkLabel, 'error', 'Falha ao baixar audio');
        $generationFailed = true;
        break;
    }

    $chunkAudioFiles[] = $chunkFile;
    debugLog('Chunk ' . ($ci + 1) . ' OK', 'ok', round(filesize($chunkFile) / 1024) . 'KB');
}

// Limpar audio de referencia temporario
if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);

// Verificar se todos os chunks foram gerados
if ($generationFailed || count($chunkAudioFiles) === 0) {
    // Limpar arquivos parciais
    foreach ($chunkAudioFiles as $f) {
        if (file_exists($f)) unlink($f);
    }
    returnError('OmniVoice falhou ao gerar o audio. Tente novamente.', 504);
}

// ===================== CONCATENAR CHUNKS (se necessario) =====================
if ($chunkCount > 1) {
    debugLog('Concatenar', 'info', "Juntando $chunkCount pedacos de audio...");

    // Detectar formato (WAV ou outro) pelo primeiro arquivo
    $first4 = @file_get_contents($chunkAudioFiles[0], false, null, 0, 4);
    $isWav = ($first4 !== false && $first4 === 'RIFF');

    if ($isWav) {
        $finalAudioFile = concatenateWavFiles($chunkAudioFiles);
    } else {
        // Para MP3 ou outros formatos, concatenacao simples
        $finalAudioFile = tempnam(sys_get_temp_dir(), 'vp_concat_') . '.wav';
        $fp = fopen($finalAudioFile, 'wb');
        foreach ($chunkAudioFiles as $f) {
            fwrite($fp, file_get_contents($f));
        }
        fclose($fp);
    }

    if (!$finalAudioFile || !file_exists($finalAudioFile)) {
        foreach ($chunkAudioFiles as $f) {
            if (file_exists($f)) unlink($f);
        }
        returnError('Falha ao juntar os pedacos de audio', 500);
    }

    debugLog('Concatenar', 'ok', "Audio final: " . round(filesize($finalAudioFile) / 1024) . 'KB (de ' . $chunkCount . ' chunks)');

    // Limpar arquivos de chunk
    foreach ($chunkAudioFiles as $f) {
        if (file_exists($f)) unlink($f);
    }
} else {
    // Apenas 1 chunk, usar diretamente
    $finalAudioFile = $chunkAudioFiles[0];
}

// ===================== RETORNAR AUDIO COMO BASE64 =====================
$audioBase64 = base64_encode(file_get_contents($finalAudioFile));
$mimeType = 'audio/wav';
$dataUri = 'data:' . $mimeType . ';base64,' . $audioBase64;

if ($finalAudioFile && file_exists($finalAudioFile)) unlink($finalAudioFile);

debugLog('FINAL', 'ok', 'OmniVoice via PHP DIRECT' . ($chunkCount > 1 ? " ($chunkCount chunks concatenados)" : '') . ' - zero Vercel');

echo json_encode([
    'audioUrl' => $dataUri,
    'model' => 'omnivoice',
    'mode' => $mode,
    'chunks' => $chunkCount,
    'viaDirectPhp' => true,
    'debug' => debugResult()
]);
?>
