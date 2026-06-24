<?php

$frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_filter([
        'http://localhost:5173',
        $frontendUrl,
    ])),

    // Memudahkan preview tugas yang menggunakan subdomain gratis Render.
    // Untuk aplikasi produksi serius, batasi hanya ke domain frontend resmi.
    'allowed_origins_patterns' => [
        '#^https://[a-z0-9-]+\.onrender\.com$#i',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
