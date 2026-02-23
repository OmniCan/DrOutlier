<?php

// Test script to verify Razorpay credentials are loading correctly
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Razorpay Configuration:\n";
echo "================================\n\n";

echo "Direct .env access:\n";
echo "RAZORPAY_KEY from env(): " . (env('RAZORPAY_KEY') ?: 'NOT SET') . "\n";
echo "RAZORPAY_SECRET from env(): " . (env('RAZORPAY_SECRET') ? 'SET (hidden)' : 'NOT SET') . "\n\n";

echo "Via config helper:\n";
echo "RAZORPAY_KEY from config(): " . (config('razorpay.key') ?: 'NOT SET') . "\n";
echo "RAZORPAY_SECRET from config(): " . (config('razorpay.secret') ? 'SET (hidden)' : 'NOT SET') . "\n\n";

if (config('razorpay.key') && config('razorpay.secret')) {
    echo "✓ Credentials are loaded correctly!\n";

    // Try to initialize Razorpay API
    try {
        $api = new \Razorpay\Api\Api(config('razorpay.key'), config('razorpay.secret'));
        echo "✓ Razorpay API initialized successfully!\n";
    } catch (\Exception $e) {
        echo "✗ Razorpay API initialization failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "✗ Credentials NOT loaded!\n";
    echo "\nPlease run one of these commands:\n";
    echo "  php artisan config:clear\n";
    echo "  php artisan config:cache\n";
}

echo "\n";
