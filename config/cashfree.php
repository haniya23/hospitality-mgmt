<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cashfree Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Cashfree payment gateway integration
    |
    */

    'app_id' => env('CASHFREE_APP_ID'),
    'secret_key' => env('CASHFREE_SECRET_KEY'),
    'mode' => env('CASHFREE_MODE', 'test'), // 'test' or 'prod'
    'webhook_secret' => env('CASHFREE_WEBHOOK_SECRET'),
    
    'base_url' => [
        'test' => 'https://sandbox.cashfree.com',
        'prod' => 'https://api.cashfree.com'
    ],
    
    'api_version' => '2022-01-01',
    
    'plans' => [
        'starter' => [
            'name' => 'Starter Plan',
            'amount' => 299, // Amount in rupees (₹299)
            'currency' => 'INR',
            'interval' => 'month',
            'interval_count' => 1,
        ],
        'professional' => [
            'name' => 'Professional Plan',
            'amount' => 999, // Amount in rupees (₹999)
            'currency' => 'INR',
            'interval' => 'month',
            'interval_count' => 1,
        ],
        'additional_accommodation' => [
            'name' => 'Additional Accommodation',
            'amount' => 99, // Amount in rupees (₹99)
            'currency' => 'INR',
            'interval' => 'month',
            'interval_count' => 1,
        ]
    ]
];
