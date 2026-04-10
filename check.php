<?php
/**
 * Diagnostic Page - Check PHP Frontend Status
 * Access this at: droutlier.com/check.php
 */

echo "<h1>DrOutlier PHP Frontend Diagnostics</h1>";

// PHP Version
echo "<h2>✓ PHP Version</h2>";
echo "<p>" . phpversion() . "</p>";

// Check vendor directory
echo "<h2>Vendor Directory</h2>";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "<p style='color:green'>✓ Vendor directory exists</p>";
} else {
    echo "<p style='color:red'>✗ Vendor directory NOT found - Run: composer install</p>";
}

// Check .env file
echo "<h2>.env File</h2>";
if (file_exists(__DIR__ . '/.env')) {
    echo "<p style='color:green'>✓ .env file exists</p>";
} else {
    echo "<p style='color:red'>✗ .env file NOT found - Copy from .env.frontend.example</p>";
}

// Check storage directory
echo "<h2>Storage Directory</h2>";
if (is_dir(__DIR__ . '/storage/cache')) {
    if (is_writable(__DIR__ . '/storage/cache')) {
        echo "<p style='color:green'>✓ storage/cache writable</p>";
    } else {
        echo "<p style='color:orange'>⚠ storage/cache NOT writable - Run: chmod 755 storage/cache</p>";
    }
} else {
    echo "<p style='color:red'>✗ storage/cache NOT found</p>";
}

// Check key directories
echo "<h2>Directory Structure</h2>";
$dirs = ['app', 'views', 'routes', 'storage'];
foreach ($dirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "<p style='color:green'>✓ /{$dir} exists</p>";
    } else {
        echo "<p style='color:red'>✗ /{$dir} NOT found</p>";
    }
}

// Check PHP extensions
echo "<h2>PHP Extensions</h2>";
$required = ['mbstring', 'json', 'curl'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color:green'>✓ {$ext}</p>";
    } else {
        echo "<p style='color:red'>✗ {$ext} NOT loaded</p>";
    }
}

// Try to load autoload
echo "<h2>Autoload Test</h2>";
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "<p style='color:green'>✓ Autoload successful</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Autoload failed: " . $e->getMessage() . "</p>";
}

// Test .env loading
echo "<h2>Environment Test</h2>";
if (class_exists('Dotenv\Dotenv')) {
    try {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        echo "<p style='color:green'>✓ .env loaded successfully</p>";
        echo "<p>APP_NAME: " . ($_ENV['APP_NAME'] ?? 'not set') . "</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ .env loading failed: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>✗ Dotenv class not found</p>";
}

echo "<hr>";
echo "<p><strong>If all checks pass, delete this file and try droutlier.com again</strong></p>";
