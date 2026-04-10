<?php
/**
 * Bookmark Proxy - Same-origin bookmark toggle to avoid /admin routing issues
 */

session_start();
require_once __DIR__ . '/includes/api-helper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_token'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$module = $_GET['module'] ?? '';
$userId = $_POST['user_id'] ?? ($_SESSION['user']['id'] ?? '');
$itemId = $_POST['item_id'] ?? '';
$watchLearnId = $_POST['watch_learn_id'] ?? '';

$moduleMap = [
    'spotters' => [
        'endpoint' => '/new-spotters/toggle-bookmark',
        'idKey' => 'item_id'
    ],
    'notes' => [
        'endpoint' => '/theory-notes/toggle-bookmark',
        'idKey' => 'item_id'
    ],
    'osce' => [
        'endpoint' => '/new-osce/toggle-bookmark',
        'idKey' => 'item_id'
    ],
    'ai-rad' => [
        'endpoint' => '/new-exam-cases/toggle-bookmark',
        'idKey' => 'item_id'
    ],
    'practical-essentials' => [
        'endpoint' => '/new-table-viva/toggle-bookmark',
        'idKey' => 'item_id'
    ],
    'watch-learn' => [
        'endpoint' => '/watch-and-learn-category/toggle-bookmark',
        'idKey' => 'watch_learn_id'
    ],
];

if (!isset($moduleMap[$module])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid module']);
    exit;
}

if (empty($userId)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
    exit;
}

$idValue = $moduleMap[$module]['idKey'] === 'watch_learn_id' ? $watchLearnId : $itemId;
if (empty($idValue)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing item id']);
    exit;
}

$token = getToken();
$url = API_BASE_URL . $moduleMap[$module]['endpoint'];
$postData = [
    'user_id' => $userId,
    $moduleMap[$module]['idKey'] => $idValue,
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Authorization: Bearer ' . $token,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Proxy request failed', 'error' => $curlError]);
    exit;
}

$decoded = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code($httpCode > 0 ? $httpCode : 500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Upstream returned non-JSON response',
        'http_code' => $httpCode,
        'raw' => substr((string)$response, 0, 500)
    ]);
    exit;
}

http_response_code($httpCode > 0 ? $httpCode : 200);
echo json_encode($decoded);
