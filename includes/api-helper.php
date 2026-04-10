<?php
/**
 * API Helper Functions
 * Simple cURL-based API client (no Guzzle dependency)
 */

// API Base URL
define('API_BASE_URL', 'https://admin.droutlier.com/api');

/**
 * Make authenticated API request
 */
function apiRequest($endpoint, $method = 'POST', $data = [], $token = null) {
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init();
    
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    } elseif ($method === 'GET') {
        if (!empty($data)) {
            $url .= '?' . http_build_query($data);
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error, 'http_code' => $httpCode];
    }
    
    $decoded = json_decode($response, true);
    return $decoded ?: ['error' => 'Invalid JSON response', 'raw' => $response];
}

/**
 * Check if user is authenticated
 */
function requireAuth() {
    if (!isset($_SESSION['user_token'])) {
        header('Location: /?login=required');
        exit;
    }
}

/**
 * Get current user from session
 */
function getUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Get auth token from session
 */
function getToken() {
    return $_SESSION['user_token'] ?? null;
}
