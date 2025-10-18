<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mercado Pago Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com o Mercado Pago
    |
    */

    'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
    'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    'webhook_secret' => env('MERCADOPAGO_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Plan Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações do plano de assinatura
    |
    */

    'plan' => [
        'name' => 'Premium',
        'price' => 29.90,
        'currency' => 'BRL',
        'interval' => 'month',
        'trial_days' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Métodos de pagamento aceitos
    |
    */

    'payment_methods' => [
        'credit_card' => [
            'enabled' => true,
            'installments' => 12,
        ],
        'pix' => [
            'enabled' => true,
        ],
        'boleto' => [
            'enabled' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para webhooks
    |
    */

    'webhook' => [
        'url' => env('APP_URL') . '/subscription/webhook',
        'events' => [
            'preapproval.authorized',
            'preapproval.paused',
            'preapproval.cancelled',
            'payment.created',
            'payment.updated',
            'payment.cancelled',
            'payment.rejected',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | Ambiente do Mercado Pago (sandbox ou production)
    |
    */

    'environment' => env('MERCADOPAGO_ENVIRONMENT', 'sandbox'),
    'sandbox' => env('MERCADOPAGO_SANDBOX', true),
];
