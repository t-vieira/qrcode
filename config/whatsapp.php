<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Business API Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com WhatsApp Business API
    |
    */

    'api_url' => env('WHATSAPP_API_URL', 'https://graph.facebook.com/v18.0'),
    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
    'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
    'support_number' => env('WHATSAPP_SUPPORT_NUMBER'),

    /*
    |--------------------------------------------------------------------------
    | Templates de Mensagem
    |--------------------------------------------------------------------------
    |
    | Templates aprovados no WhatsApp Business
    |
    */

    'templates' => [
        'welcome' => 'welcome_qr_saas',
        'subscription_activated' => 'subscription_activated',
        'trial_expiring' => 'trial_expiring',
        'support_response' => 'support_response',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Notificação
    |--------------------------------------------------------------------------
    |
    | Configurações para envio de notificações automáticas
    |
    */

    'notifications' => [
        'welcome' => [
            'enabled' => env('WHATSAPP_WELCOME_ENABLED', true),
            'delay_minutes' => 5, // Delay após criação da conta
        ],
        'trial_expiring' => [
            'enabled' => env('WHATSAPP_TRIAL_EXPIRING_ENABLED', true),
            'days_before' => [3, 1], // Dias antes da expiração
        ],
        'subscription_activated' => [
            'enabled' => env('WHATSAPP_SUBSCRIPTION_ACTIVATED_ENABLED', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Suporte
    |--------------------------------------------------------------------------
    |
    | Configurações para o sistema de suporte
    |
    */

    'support' => [
        'enabled' => env('WHATSAPP_SUPPORT_ENABLED', true),
        'business_hours' => [
            'start' => '09:00',
            'end' => '18:00',
            'timezone' => 'America/Sao_Paulo',
            'weekdays' => [1, 2, 3, 4, 5], // Segunda a sexta
        ],
        'auto_response' => [
            'enabled' => true,
            'message' => 'Olá! Recebemos sua mensagem e responderemos em breve. Nossa equipe de suporte funciona de segunda a sexta, das 9h às 18h.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Webhook
    |--------------------------------------------------------------------------
    |
    | Configurações para webhook do WhatsApp
    |
    */

    'webhook' => [
        'url' => env('APP_URL') . '/whatsapp/webhook',
        'events' => [
            'messages',
            'message_deliveries',
            'message_reads',
            'message_reactions',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Limites de envio de mensagens
    |
    */

    'rate_limits' => [
        'messages_per_minute' => 20,
        'messages_per_hour' => 1000,
        'messages_per_day' => 10000,
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações de Log
    |--------------------------------------------------------------------------
    |
    | Configurações para logging
    |
    */

    'logging' => [
        'enabled' => env('WHATSAPP_LOGGING_ENABLED', true),
        'log_messages' => env('WHATSAPP_LOG_MESSAGES', false),
        'log_webhooks' => env('WHATSAPP_LOG_WEBHOOKS', true),
    ],
];
