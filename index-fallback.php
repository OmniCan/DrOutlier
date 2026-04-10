<?php

/**
 * DrOutlier Frontend - Entry Point (Fallback - No Dotenv)
 * Simple PHP Router with Twig Templating
 * 
 * INSTRUCTIONS:
 * 1. Rename this to index.php if Dotenv is missing
 * 2. Edit the config section below with your values
 */

// ============================================
// CONFIGURATION (Edit these values)
// ============================================
$_ENV = [
    'APP_NAME' => 'DrOutlier',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'APP_URL' => 'https://www.droutlier.com',
    
    // Laravel Admin API
    'API_BASE_URL' => 'https://admin.droutlier.com/api',
    'API_TIMEOUT' => '30',
    
    // Session
    'SESSION_LIFETIME' => '120',
    'SESSION_COOKIE_NAME' => 'droutlier_session',
];

// Load .env file manually if it exists
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// ============================================
// APPLICATION START
// ============================================

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Router;
use App\Core\View;

// Start session
session_start();

// Initialize Twig
View::init();

// Initialize Router
$router = new Router();

// Include route definitions
require_once __DIR__ . '/routes/web.php';

// Dispatch the request
$router->dispatch();
