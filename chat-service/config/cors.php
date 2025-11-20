<?php
declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth', '*'], // Asegúrate que api/* esté incluido

    'allowed_methods' => ['*'], // Permitir POST, GET, OPTIONS, etc.

    'allowed_origins' => [
        'http://localhost:8020', // Tu frontend (my-chat)
        'http://127.0.0.1:8020',
        'http://localhost:5173', // Vite en desarrollo
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Permitir Content-Type y X-Requested-With

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false, // true si usas cookies/sanctum, false si pasas IDs a mano

];
