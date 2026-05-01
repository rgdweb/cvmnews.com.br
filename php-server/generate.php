<?php
// generate.php - Geracao de voz TTS via OmniVoice (HuggingFace Space)
// Este arquivo BYPASSA o Vercel para evitar o timeout de 60s do plano Hobby
// Recebe os parametros via POST JSON e retorna audio base64

set_time_limit(0);
ini_set('max_input_time', 0);
ini_set('memory_limit', '256M');

// CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Metodo nao permitido']);
    exit;
}

require_once __DIR__ . '/config.php';

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

// ===================== LER INPUT JSON =====================
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input) {
    returnError('JSON invalido ou vazio', 400);
}

$texto = $input['text'] ?? '';
$idioma = $input['language'] ?? 'Auto';
$refAudioUrl = $input['refAudioUrl'] ?? '';   // URL do PHP server (local)
$refAudioPath = $input['refAudioPath'] ?? '';  // Path no HF Space (fallback)
$refText = $input['refText'] ?? '';
$instruct = $input['instruct'] ?? '';
$refAudioName = $input['refAudioName'] ?? 'ref_audio.wav';
$speed = $input['speed'] ?? 1.0;
$numStep = $input['numStep'] ?? 32;
$guidanceScale = $input['guidanceScale'] ?? 2.0;

debugLog('Input recebido', 'info', "texto: " . mb_substr($texto, 0, 50) . " | idioma: $idioma | steps: $numStep");

// Validacoes
if (empty(trim($texto))) {
    returnError('Texto e obrigatorio', 400);
}
if (empty($refAudioUrl) && empty($refAudioPath)) {
    returnError('Audio de referencia nao fornecido', 400);
}

$hfUrl = defined('HF_SPACE_URL') ? HF_SPACE_URL : 'https://k2-fsa-omnivoice.hf.space';
debugLog('HF Space', 'info', $hfUrl);

// ===================== BAIXAR REF AUDIO =====================
$refAudioFile = null;
$tempRefFile = null;

if (!empty($refAudioUrl)) {
    debugLog('Download ref audio', 'info', 'de: ' . mb_substr($refAudioUrl, 0, 80));
    $tempRefFile = tempnam(sys_get_temp_dir(), 'vp_ref_') . '.' . pathinfo($refAudioName, PATHINFO_EXTENSION);

    $ch = curl_init($refAudioUrl);
    $fp = fopen($tempRefFile, 'w');
    curl_setopt_array($ch, [
        CURLOPT_FILE => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $dlOk = curl_exec($ch);
    $dlHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);

    if ($dlOk && $dlHttpCode == 200 && filesize($tempRefFile) > 0) {
        $refAudioFile = $tempRefFile;
        debugLog('Download ref audio', 'ok', round(filesize($tempRefFile) / 1024) . 'KB');
    } else {
        debugLog('Download ref audio', 'error', "HTTP $dlHttpCode");
        if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);
    }
}

// ===================== UPLOAD PARA HF SPACE =====================
$hfFilePath = null;

if ($refAudioFile && file_exists($refAudioFile)) {
    debugLog('Upload para HF', 'info', 'enviando arquivo...');

    $ch = curl_init($hfUrl . '/gradio_api/upload');
    $cfile = new CURLFile($refAudioFile, mime_content_type($refAudioFile), $refAudioName);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => ['files' => $cfile],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $uploadResp = curl_exec($ch);
    $uploadCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($uploadCode == 200 && $uploadResp) {
        $uploadData = json_decode($uploadResp, true);
        if (is_array($uploadData) && count($uploadData) > 0) {
            $hfFilePath = $uploadData[0];
            debugLog('Upload para HF', 'ok', $hfFilePath);
        } else {
            debugLog('Upload para HF', 'error', 'resposta inesperada: ' . mb_substr($uploadResp, 0, 200));
        }
    } else {
        debugLog('Upload para HF', 'error', "HTTP $uploadCode: " . mb_substr($uploadResp ?: 'sem resposta', 0, 200));
    }
}

// Fallback para path existente no HF
if (!$hfFilePath && !empty($refAudioPath)) {
    $hfFilePath = $refAudioPath;
    debugLog('Fallback HF path', 'info', $hfFilePath);
}

if (!$hfFilePath) {
    if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);
    returnError('Nao foi possivel obter o audio de referencia no HF Space');
}

// ===================== SUBMIT PARA GRADIO =====================
$gradioData = [
    $texto,
    $idioma,
    [
        'path' => $hfFilePath,
        'orig_name' => $refAudioName,
        'mime_type' => (pathinfo($refAudioName, PATHINFO_EXTENSION) === 'mp3') ? 'audio/mpeg' : 'audio/wav',
        'is_stream' => false,
        'meta' => ['_type' => 'gradio.FileData']
    ],
    $refText,
    $instruct,
    (int)$numStep,
    (float)$guidanceScale,
    true,    // denoise
    (float)$speed,
    null,    // duration
    true,    // preprocess_prompt
    true     // postprocess_output
];

debugLog('Submit Gradio', 'info', 'enviando job...');

$submitBody = json_encode(['data' => $gradioData]);

$ch = curl_init($hfUrl . '/gradio_api/call/_clone_fn');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $submitBody,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 60,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$submitResp = curl_exec($ch);
$submitCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($submitCode != 200 || !$submitResp) {
    if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);
    returnError('Falha ao enviar para o Gradio: HTTP ' . $submitCode, 502);
}

$submitData = json_decode($submitResp, true);
$eventId = $submitData['event_id'] ?? null;

if (!$eventId) {
    if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);
    debugLog('Submit Gradio', 'error', 'sem event_id: ' . mb_substr($submitResp, 0, 200));
    returnError('Nenhum event_id retornado pelo Gradio', 502);
}

debugLog('Submit Gradio', 'ok', "event_id: $eventId");

// ===================== POLLING DO RESULTADO =====================
debugLog('Polling', 'info', "aguardando resultado de $eventId...");
$audioUrl = null;
$maxPolls = 180;       // 180 tentativas
$pollInterval = 1500000; // 1.5 segundo em microssegundos

for ($i = 0; $i < $maxPolls; $i++) {
    usleep($pollInterval);

    $ch = curl_init($hfUrl . '/gradio_api/call/_clone_fn/' . $eventId);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTPHEADER => ['Accept: text/event-stream'],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);
    $pollResp = curl_exec($ch);
    $pollCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($pollCode != 200 || !$pollResp) {
        if ($i % 15 == 0) {
            debugLog('Poll', 'warn', "HTTP $pollCode na tentativa " . ($i + 1));
        }
        continue;
    }

    // Parsear blocos SSE
    $blocks = explode("\n\n", trim($pollResp));

    foreach ($blocks as $block) {
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

        // Evento COMPLETE = audio gerado
        if ($eventType === 'complete' && !empty($eventData)) {
            debugLog('Poll', 'ok', "complete na tentativa " . ($i + 1));

            $resultData = json_decode($eventData, true);
            if (is_array($resultData) && count($resultData) >= 2) {
                $audioOutput = $resultData[0];
                if (isset($audioOutput['url'])) {
                    $audioUrl = $audioOutput['url'];
                } elseif (isset($audioOutput['path'])) {
                    $audioUrl = $hfUrl . '/gradio_api/file=' . $audioOutput['path'];
                }
            }

            if ($audioUrl) {
                debugLog('Audio gerado', 'ok', mb_substr($audioUrl, 0, 100));
            } else {
                if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);
                returnError('Audio gerado mas sem URL no output');
            }
            break 2; // sair dos dois loops
        }

        // Evento ERROR
        if ($eventType === 'error') {
            debugLog('Gradio ERROR', 'error', mb_substr($eventData ?: 'vazio', 0, 500));

            // Erro null = Gradio ocupado ou space acordando
            if (empty($eventData) || $eventData === 'null') {
                // Esperar mais e continuar polling (o event_id ainda pode resolver)
                debugLog('Gradio null error', 'warn', 'Gradio retornou null, possivelmente ocupado. Aguardando...');
                usleep(5000000); // 5s extra
                continue 2; // continuar o loop principal de polling
            }

            // Erro real
            $errorMsg = 'Erro na geracao pelo servidor de IA';
            if (!empty($eventData)) {
                $errParsed = json_decode($eventData, true);
                if ($errParsed) {
                    $errorMsg = $errParsed['error'] ?? $errParsed['message'] ?? $errorMsg;
                } elseif (strlen($eventData) > 5 && strlen($eventData) < 500) {
                    $errorMsg = $eventData;
                }
            }
            if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);
            returnError($errorMsg, 500);
        }

        // Heartbeat = sem resultado ainda
        if ($eventType === 'heartbeat') {
            debugLog('Poll', 'warn', 'heartbeat recebido');
            break 2;
        }
    }

    if ($audioUrl) break;
}

if (!$audioUrl) {
    if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);
    returnError('Tempo limite excedido na geracao (aprox 4.5 min)', 504);
}

// ===================== BAIXAR AUDIO GERADO =====================
debugLog('Download audio gerado', 'info', 'baixando...');
$tempAudioFile = tempnam(sys_get_temp_dir(), 'vp_gen_') . '.wav';

$ch = curl_init($audioUrl);
$fp = fopen($tempAudioFile, 'w');
curl_setopt_array($ch, [
    CURLOPT_FILE => $fp,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 120,
    CURLOPT_SSL_VERIFYPEER => false,
]);
$dlOk = curl_exec($ch);
$dlCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
fclose($fp);

if (!$dlOk || $dlCode != 200 || filesize($tempAudioFile) == 0) {
    if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);
    if ($tempAudioFile && file_exists($tempAudioFile)) unlink($tempAudioFile);
    returnError("Falha ao baixar audio gerado (HTTP $dlCode)");
}

$audioSize = filesize($tempAudioFile);
debugLog('Download audio gerado', 'ok', round($audioSize / 1024) . 'KB');

// ===================== CONVERTER PARA BASE64 =====================
debugLog('Base64 encode', 'info', 'convertendo...');
$audioBase64 = base64_encode(file_get_contents($tempAudioFile));

// Detectar mime type
$ext = strtolower(pathinfo($audioUrl, PATHINFO_EXTENSION));
$mimeType = ($ext === 'mp3') ? 'audio/mpeg' : 'audio/wav';

$dataUri = 'data:' . $mimeType . ';base64,' . $audioBase64;
debugLog('Base64 encode', 'ok', round(strlen($audioBase64) / 1024) . 'KB base64');

// ===================== LIMPAR ARQUIVOS TEMP =====================
if ($tempRefFile && file_exists($tempRefFile)) unlink($tempRefFile);
if ($tempAudioFile && file_exists($tempAudioFile)) unlink($tempAudioFile);

// ===================== RETORNAR =====================
debugLog('FINAL', 'ok', 'audio pronto via PHP (sem Vercel)');

echo json_encode([
    'audioUrl' => $dataUri,
    'mixed' => false,
    'viaPhp' => true,
    'debug' => debugResult()
]);
?>
