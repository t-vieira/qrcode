<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Configurações de Tradução
    |--------------------------------------------------------------------------
    |
    | Aqui você pode configurar as opções de tradução e internacionalização
    | do sistema QR Code SaaS.
    |
    */

    'default_locale' => 'pt_BR',
    'fallback_locale' => 'en',
    'available_locales' => [
        'pt_BR' => 'Português (Brasil)',
        'en' => 'English',
    ],

    'date_format' => 'd/m/Y',
    'datetime_format' => 'd/m/Y H:i',
    'time_format' => 'H:i',

    'currency' => [
        'code' => 'BRL',
        'symbol' => 'R$',
        'position' => 'before', // before, after
        'decimals' => 2,
        'thousands_separator' => '.',
        'decimal_separator' => ',',
    ],

    'number_format' => [
        'decimals' => 2,
        'thousands_separator' => '.',
        'decimal_separator' => ',',
    ],

    'timezone' => 'America/Sao_Paulo',

    'rtl_locales' => [
        // Locales que usam escrita da direita para esquerda
    ],

    'pluralization' => [
        'pt_BR' => [
            'zero' => 'Nenhum',
            'one' => 'Um',
            'other' => ':count',
        ],
        'en' => [
            'zero' => 'None',
            'one' => 'One',
            'other' => ':count',
        ],
    ],

    'validation_messages' => [
        'custom' => [
            'attributes' => [
                'name' => 'nome',
                'email' => 'e-mail',
                'password' => 'senha',
                'password_confirmation' => 'confirmação da senha',
                'phone' => 'telefone',
                'address' => 'endereço',
                'city' => 'cidade',
                'state' => 'estado',
                'zip' => 'CEP',
                'country' => 'país',
                'website' => 'site',
                'description' => 'descrição',
                'title' => 'título',
                'content' => 'conteúdo',
                'message' => 'mensagem',
                'subject' => 'assunto',
                'file' => 'arquivo',
                'image' => 'imagem',
                'logo' => 'logo',
                'url' => 'URL',
                'short_code' => 'código curto',
                'domain' => 'domínio',
                'folder' => 'pasta',
                'team' => 'equipe',
                'role' => 'função',
                'permissions' => 'permissões',
                'subscription' => 'assinatura',
                'plan' => 'plano',
                'amount' => 'valor',
                'status' => 'status',
                'type' => 'tipo',
                'format' => 'formato',
                'resolution' => 'resolução',
                'color' => 'cor',
                'design' => 'design',
                'custom_domain' => 'domínio customizado',
                'trial_ends_at' => 'fim do período de teste',
                'subscription_status' => 'status da assinatura',
                'subscription_id' => 'ID da assinatura',
                'mp_subscription_id' => 'ID da assinatura Mercado Pago',
                'mp_preapproval_id' => 'ID da pré-aprovação Mercado Pago',
                'current_period_start' => 'início do período atual',
                'current_period_end' => 'fim do período atual',
                'canceled_at' => 'data de cancelamento',
                'deletion_requested_at' => 'data da solicitação de exclusão',
                'deletion_reason' => 'motivo da exclusão',
            ],
        ],
    ],

    'cache' => [
        'enabled' => env('TRANSLATION_CACHE_ENABLED', true),
        'key' => 'translations',
        'ttl' => 3600, // 1 hora
    ],

    'auto_translate' => [
        'enabled' => env('AUTO_TRANSLATE_ENABLED', false),
        'provider' => env('AUTO_TRANSLATE_PROVIDER', 'google'),
        'api_key' => env('AUTO_TRANSLATE_API_KEY'),
    ],
];
