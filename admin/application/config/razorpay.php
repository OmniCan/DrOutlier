<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Razorpay API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure your Razorpay API credentials. You can get your
    | API key and secret from the Razorpay Dashboard.
    |
    */

    'key' => env('RAZORPAY_KEY'),
    'secret' => env('RAZORPAY_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Razorpay Webhook Secret
    |--------------------------------------------------------------------------
    |
    | The webhook secret is used to verify webhook requests from Razorpay.
    | You can find this in your Razorpay Dashboard under Webhooks.
    |
    */

    'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Razorpay Currency
    |--------------------------------------------------------------------------
    |
    | The currency to be used for all transactions. Default is INR.
    |
    */

    'currency' => env('RAZORPAY_CURRENCY', 'INR'),

    /*
    |--------------------------------------------------------------------------
    | Payment Capture
    |--------------------------------------------------------------------------
    |
    | Whether to capture payments automatically or manually.
    | Set to 1 for automatic capture, 0 for manual capture.
    |
    */

    'payment_capture' => env('RAZORPAY_PAYMENT_CAPTURE', 1),
];
