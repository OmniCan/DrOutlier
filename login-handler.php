<?php
/**
 * Login Session Handler
 * Receives login data from modal and stores in session
 */

session_start();
header('Content-Type: application/json');

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['token']) || !isset($data['user'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid data']);
    exit;
}

// Store in session
$_SESSION['user_token'] = $data['token'];
$_SESSION['user'] = $data['user'];
$_SESSION['user_id'] = $data['user']['id'] ?? '';

echo json_encode(['success' => true]);
exit;
