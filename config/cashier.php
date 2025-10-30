<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hardcoded Admin Credentials
    |--------------------------------------------------------------------------
    |
    | This configuration file contains a simple hardcoded admin identity used
    | for the single-admin login requirement. In production you should move
    | credentials to environment variables or a secure secrets manager.
    |
    */

    'admin' => [
        'email' => 'admin@cashier.com',
        // NOTE: stored in plain text here only for the demo. Replace in prod.
        'password' => 'secret123',
        'name' => 'Administrator',
    ],

    // Default currency symbol used across the app
    'currency' => env('CASHIER_CURRENCY', '$'),

    // Tax configuration
    'tax' => [
        'enabled' => env('TAX_ENABLED', true),
        'default_rate' => env('TAX_DEFAULT_RATE', 15), // Default 15% VAT
        'label' => env('TAX_LABEL', 'VAT'),
    ],

    // Discount configuration
    'discount' => [
        'max_without_approval' => 5, // Maximum discount % without admin approval
        'enabled' => true,
    ],

    // Company/Shop information for invoices
    'company' => [
        'name' => env('COMPANY_NAME', 'Lumi POS'),
        'address' => env('COMPANY_ADDRESS', '123 Main Street'),
        'city' => env('COMPANY_CITY', 'Your City, State 12345'),
        'phone' => env('COMPANY_PHONE', '+1 (555) 123-4567'),
        'email' => env('COMPANY_EMAIL', 'info@lumipos.com'),
        'website' => env('COMPANY_WEBSITE', 'www.lumipos.com'),
        'tax_id' => env('COMPANY_TAX_ID', ''),
    ],
];
