<?php

/**
 * DrOutlier Frontend - Entry Point
 * Simple PHP Router with Twig Templating
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Core\View;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Start session
session_start();

// Initialize Twig
View::init();

// Initialize Router
$router = new Router();

// Include route definitions
require_once __DIR__ . '/../routes/web.php';

// Dispatch the request
$router->dispatch();
