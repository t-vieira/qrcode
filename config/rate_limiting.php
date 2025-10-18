<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações personalizadas de rate limiting para diferentes tipos
    | de requisições e usuários.
    |
    */

    'limits' => [
        // Limites para autenticação
        'auth' => [
            'login' => [
                'max_attempts' => 5,
                'decay_minutes' => 5,
                'key' => 'login',
            ],
            'register' => [
                'max_attempts' => 3,
                'decay_minutes' => 10,
                'key' => 'register',
            ],
            'password_reset' => [
                'max_attempts' => 3,
                'decay_minutes' => 10,
                'key' => 'password_reset',
            ],
        ],

        // Limites para API
        'api' => [
            'general' => [
                'max_attempts' => 100,
                'decay_minutes' => 1,
                'key' => 'api',
            ],
            'webhook' => [
                'max_attempts' => 100,
                'decay_minutes' => 1,
                'key' => 'webhook',
            ],
            'qr_generation' => [
                'max_attempts' => 20,
                'decay_minutes' => 1,
                'key' => 'qr_generation',
            ],
        ],

        // Limites para uploads
        'upload' => [
            'file' => [
                'max_attempts' => 10,
                'decay_minutes' => 1,
                'key' => 'file_upload',
            ],
            'image' => [
                'max_attempts' => 15,
                'decay_minutes' => 1,
                'key' => 'image_upload',
            ],
        ],

        // Limites para QR Code operations
        'qrcode' => [
            'create' => [
                'max_attempts' => 10,
                'decay_minutes' => 1,
                'key' => 'qrcode_create',
            ],
            'download' => [
                'max_attempts' => 50,
                'decay_minutes' => 1,
                'key' => 'qrcode_download',
            ],
            'scan' => [
                'max_attempts' => 1000,
                'decay_minutes' => 1,
                'key' => 'qrcode_scan',
            ],
        ],

        // Limites para relatórios
        'reports' => [
            'export' => [
                'max_attempts' => 5,
                'decay_minutes' => 5,
                'key' => 'report_export',
            ],
            'generate' => [
                'max_attempts' => 10,
                'decay_minutes' => 1,
                'key' => 'report_generate',
            ],
        ],

        // Limites para suporte
        'support' => [
            'ticket_create' => [
                'max_attempts' => 5,
                'decay_minutes' => 10,
                'key' => 'support_ticket',
            ],
            'message_send' => [
                'max_attempts' => 10,
                'decay_minutes' => 1,
                'key' => 'support_message',
            ],
        ],

        // Limites para pagamentos
        'payment' => [
            'create' => [
                'max_attempts' => 5,
                'decay_minutes' => 5,
                'key' => 'payment_create',
            ],
            'webhook' => [
                'max_attempts' => 100,
                'decay_minutes' => 1,
                'key' => 'payment_webhook',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting by User Type
    |--------------------------------------------------------------------------
    |
    | Limites diferentes baseados no tipo de usuário
    |
    */

    'user_limits' => [
        'trial' => [
            'qr_codes_per_hour' => 10,
            'scans_per_hour' => 1000,
            'exports_per_day' => 3,
        ],
        'premium' => [
            'qr_codes_per_hour' => 100,
            'scans_per_hour' => 10000,
            'exports_per_day' => 50,
        ],
        'admin' => [
            'qr_codes_per_hour' => 1000,
            'scans_per_hour' => 100000,
            'exports_per_day' => 1000,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IP-based Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Limites baseados em IP para prevenir abuso
    |
    */

    'ip_limits' => [
        'general' => [
            'max_requests' => 1000,
            'decay_minutes' => 1,
        ],
        'suspicious' => [
            'max_requests' => 10,
            'decay_minutes' => 5,
        ],
        'blocked' => [
            'max_requests' => 0,
            'decay_minutes' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Exceptions
    |--------------------------------------------------------------------------
    |
    | IPs ou usuários que devem ser excluídos do rate limiting
    |
    */

    'exceptions' => [
        'ips' => [
            // Adicionar IPs confiáveis aqui
            '127.0.0.1',
            '::1',
        ],
        'user_agents' => [
            // User agents confiáveis
            'MercadoPago-Webhook',
            'WhatsApp-Business-API',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Messages
    |--------------------------------------------------------------------------
    |
    | Mensagens personalizadas para diferentes tipos de rate limiting
    |
    */

    'messages' => [
        'default' => 'Muitas tentativas. Tente novamente em :seconds segundos.',
        'login' => 'Muitas tentativas de login. Tente novamente em :seconds segundos.',
        'register' => 'Muitas tentativas de registro. Tente novamente em :seconds segundos.',
        'password_reset' => 'Muitas tentativas de redefinição de senha. Tente novamente em :seconds segundos.',
        'api' => 'Limite de API excedido. Tente novamente em :seconds segundos.',
        'upload' => 'Muitos uploads. Tente novamente em :seconds segundos.',
        'qrcode' => 'Muitas operações de QR Code. Tente novamente em :seconds segundos.',
        'reports' => 'Muitas exportações de relatório. Tente novamente em :seconds segundos.',
        'support' => 'Muitas mensagens de suporte. Tente novamente em :seconds segundos.',
        'payment' => 'Muitas tentativas de pagamento. Tente novamente em :seconds segundos.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Storage
    |--------------------------------------------------------------------------
    |
    | Configurações de armazenamento para rate limiting
    |
    */

    'storage' => [
        'driver' => env('RATE_LIMITING_DRIVER', 'cache'),
        'prefix' => 'rate_limit:',
        'ttl' => 3600, // 1 hora em segundos
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Monitoring
    |--------------------------------------------------------------------------
    |
    | Configurações para monitoramento de rate limiting
    |
    */

    'monitoring' => [
        'enabled' => env('RATE_LIMITING_MONITORING', true),
        'log_threshold' => 10, // Log quando exceder este número de tentativas
        'alert_threshold' => 50, // Alertar quando exceder este número
        'alert_email' => env('RATE_LIMITING_ALERT_EMAIL'),
    ],
];
