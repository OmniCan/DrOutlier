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
        ['endpoint' => '/new-spotters/toggle-bookmark', 'idKey' => 'item_id'],
        ['endpoint' => '/spotters/change-bookmark-status', 'idKey' => 'spotter_id'],
    ],
    'notes' => [
        ['endpoint' => '/theory-notes/toggle-bookmark', 'idKey' => 'item_id'],
        ['endpoint' => '/theory-notes/change-bookmark', 'idKey' => 'item_id'],
        ['endpoint' => '/note/change-note-bookmark-status', 'idKey' => 'blog_id'],
    ],
    'osce' => [
        ['endpoint' => '/new-osce/toggle-bookmark', 'idKey' => 'item_id'],
        ['endpoint' => '/osce/change-osce-bookmark', 'idKey' => 'osce_id'],
    ],
    'ai-rad' => [
        ['endpoint' => '/new-exam-cases/toggle-bookmark', 'idKey' => 'item_id'],
        ['endpoint' => '/new-exam-cases/change-bookmark', 'idKey' => 'item_id'],
        ['endpoint' => '/category-munchie/change-munchie-bookmark-status', 'idKey' => 'munchie_id'],
    ],
    'practical-essentials' => [
        ['endpoint' => '/new-table-viva/toggle-bookmark', 'idKey' => 'item_id'],
        ['endpoint' => '/new-table-viva/change-bookmark', 'idKey' => 'item_id'],
        ['endpoint' => '/basic-category/change-basic-bookmark-status', 'idKey' => 'basic_id'],
    ],
    'watch-learn' => [
        ['endpoint' => '/watch-and-learn-category/toggle-bookmark', 'idKey' => 'watch_learn_id'],
        ['endpoint' => '/watch-and-learn-category/change-watch-bookmark-status', 'idKey' => 'watch_id'],
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

$token = getToken();
$candidates = $moduleMap[$module];

$lastFailure = [
    'status' => 'error',
    'message' => 'No bookmark endpoint succeeded',
    'detail' => null,
];

foreach ($candidates as $candidate) {
    $idKey = $candidate['idKey'];
    $idValue = $idKey === 'watch_learn_id' || $idKey === 'watch_id' ? $watchLearnId : $itemId;

    if (empty($idValue)) {
        continue;
    }

    $url = API_BASE_URL . $candidate['endpoint'];
    $postData = [
        'user_id' => $userId,
        $idKey => $idValue,
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
        $lastFailure = [
            'status' => 'error',
            'message' => 'Proxy request failed',
            'detail' => $curlError,
        ];
        continue;
    }

    $decoded = json_decode((string)$response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $raw = substr((string)$response, 0, 500);
        if (stripos($raw, 'could not be found') !== false || $httpCode === 404) {
            $lastFailure = [
                'status' => 'error',
                'message' => 'Endpoint not found',
                'detail' => $candidate['endpoint'],
            ];
            continue;
        }

        $lastFailure = [
            'status' => 'error',
            'message' => 'Upstream returned non-JSON response',
            'detail' => $raw,
        ];
        continue;
    }

    // Success path
    if ($httpCode >= 200 && $httpCode < 300) {
        http_response_code($httpCode);
        echo json_encode($decoded);
        exit;
    }

    // Keep last structured error, try next fallback
    $lastFailure = [
        'status' => 'error',
        'message' => $decoded['message'] ?? 'Upstream error',
        'detail' => $candidate['endpoint'],
        'http_code' => $httpCode,
    ];
}

http_response_code(404);
echo json_encode($lastFailure);
