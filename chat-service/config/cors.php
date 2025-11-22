<?php
declare(strict_types=1);

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', '*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://localhost',       // <--- IMPORTANTE: Nginx SSL
        'http://localhost',        // Por si entras por HTTP antes de redirigir
        'http://localhost:8020',   // Backup para dev directo
        'http://localhost:5173',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false, // O true si decides compartir cookies
];
