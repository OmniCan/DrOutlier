<?php
/**
 * Search Proxy - Same-origin search forwarding to Laravel API
 */

session_start();
require_once __DIR__ . '/includes/api-helper.php';

header('Content-Type: application/json');

$title = $_POST['title'] ?? $_GET['title'] ?? '';
if (strlen(trim($title)) < 2) {
    echo json_encode(['status' => 'success', 'data' => ['datalist' => []]]);
    exit;
}

$token = $_SESSION['user_token'] ?? null;

$result = apiRequest('/search', 'POST', ['title' => $title], $token);

if ($result === false) {
    http_response_code(502);
    echo json_encode(['status' => 'error', 'message' => 'Search unavailable']);
    exit;
}

echo $result;
