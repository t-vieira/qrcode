<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Headers Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for security headers and CSP.
    |
    */

    'csp' => [
        'enabled' => env('CSP_ENABLED', true),
        'report_only' => env('CSP_REPORT_ONLY', false),
        'report_uri' => env('CSP_REPORT_URI', null),
    ],

    'headers' => [
        'x_content_type_options' => env('X_CONTENT_TYPE_OPTIONS', 'nosniff'),
        'x_frame_options' => env('X_FRAME_OPTIONS', 'DENY'),
        'x_xss_protection' => env('X_XSS_PROTECTION', '1; mode=block'),
        'referrer_policy' => env('REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'permissions_policy' => env('PERMISSIONS_POLICY', 'geolocation=(), microphone=(), camera=()'),
        'strict_transport_security' => env('STRICT_TRANSPORT_SECURITY', true),
    ],

    'csp_directives' => [
        'default-src' => ["'self'"],
        'script-src' => [
            "'self'",
            "'unsafe-inline'", // Para Alpine.js
            "'unsafe-eval'", // Para Chart.js
            'https://www.google.com',
            'https://www.gstatic.com',
            'https://www.google-analytics.com',
            'https://www.googletagmanager.com',
            'https://cdn.jsdelivr.net',
            'https://unpkg.com',
        ],
        'style-src' => [
            "'self'",
            "'unsafe-inline'", // Para Tailwind CSS
            'https://fonts.googleapis.com',
        ],
        'font-src' => [
            "'self'",
            'https://fonts.gstatic.com',
            'data:',
        ],
        'img-src' => [
            "'self'",
            'data:',
            'blob:',
            'https:',
        ],
        'connect-src' => [
            "'self'",
            'https://api.mercadopago.com',
            'https://graph.facebook.com',
            'https://api.whatsapp.com',
            'https://ipapi.co',
            'https://ip-api.com',
        ],
        'media-src' => [
            "'self'",
            'data:',
            'blob:',
        ],
        'object-src' => ["'none'"],
        'base-uri' => ["'self'"],
        'form-action' => ["'self'"],
        'frame-ancestors' => ["'none'"],
    ],
];
