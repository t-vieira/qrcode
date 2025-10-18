<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QR Code Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações específicas para geração de QR Codes, otimizadas
    | para servidor compartilhado.
    |
    */

    'max_resolution' => env('QR_CODE_MAX_RESOLUTION', 1500),
    'min_resolution' => env('QR_CODE_MIN_RESOLUTION', 100),
    'default_resolution' => env('QR_CODE_DEFAULT_RESOLUTION', 300),

    'supported_formats' => explode(',', env('QR_CODE_SUPPORTED_FORMATS', 'png,jpg,svg,eps')),
    'default_format' => env('QR_CODE_DEFAULT_FORMAT', 'png'),

    'max_file_size' => env('QR_CODE_MAX_FILE_SIZE', 5242880), // 5MB
    'allowed_mime_types' => explode(',', env('QR_CODE_ALLOWED_MIME_TYPES', 'image/jpeg,image/png,image/gif,image/webp,image/svg+xml')),

    'storage' => [
        'disk' => env('QR_CODE_STORAGE_DISK', 'public'),
        'path' => env('QR_CODE_STORAGE_PATH', 'qrcodes'),
        'visibility' => env('QR_CODE_STORAGE_VISIBILITY', 'public'),
    ],

    'cache' => [
        'enabled' => env('QR_CODE_CACHE_ENABLED', true),
        'ttl' => env('QR_CODE_CACHE_TTL', 3600), // 1 hora
        'key_prefix' => env('QR_CODE_CACHE_KEY_PREFIX', 'qrcode:'),
    ],

    'optimization' => [
        'compress_images' => env('QR_CODE_COMPRESS_IMAGES', true),
        'quality' => env('QR_CODE_IMAGE_QUALITY', 85),
        'progressive' => env('QR_CODE_PROGRESSIVE_JPEG', true),
    ],

    'security' => [
        'validate_content' => env('QR_CODE_VALIDATE_CONTENT', true),
        'max_content_length' => env('QR_CODE_MAX_CONTENT_LENGTH', 10000),
        'allowed_protocols' => explode(',', env('QR_CODE_ALLOWED_PROTOCOLS', 'http,https,mailto,tel,sms,wifi')),
    ],

    'shared_hosting' => [
        'enabled' => env('SHARED_HOSTING', false),
        'memory_limit' => env('QR_CODE_MEMORY_LIMIT', 128), // MB
        'execution_time_limit' => env('QR_CODE_EXECUTION_TIME_LIMIT', 30), // segundos
        'batch_size' => env('QR_CODE_BATCH_SIZE', 10),
    ],

    'types' => [
        'url' => [
            'enabled' => true,
            'max_length' => 2048,
            'require_https' => false,
        ],
        'text' => [
            'enabled' => true,
            'max_length' => 10000,
        ],
        'email' => [
            'enabled' => true,
            'max_subject_length' => 200,
            'max_body_length' => 1000,
        ],
        'phone' => [
            'enabled' => true,
            'max_length' => 20,
        ],
        'sms' => [
            'enabled' => true,
            'max_message_length' => 1000,
        ],
        'wifi' => [
            'enabled' => true,
            'max_ssid_length' => 32,
            'max_password_length' => 63,
        ],
        'vcard' => [
            'enabled' => true,
            'max_fields' => 50,
        ],
        'business' => [
            'enabled' => true,
            'max_description_length' => 500,
        ],
        'coupon' => [
            'enabled' => true,
            'max_code_length' => 50,
        ],
        'mp3' => [
            'enabled' => true,
            'max_url_length' => 2048,
        ],
        'pdf' => [
            'enabled' => true,
            'max_url_length' => 2048,
        ],
        'image' => [
            'enabled' => true,
            'max_url_length' => 2048,
        ],
        'video' => [
            'enabled' => true,
            'max_url_length' => 2048,
        ],
        'app' => [
            'enabled' => true,
            'max_url_length' => 2048,
        ],
        'menu' => [
            'enabled' => true,
            'max_url_length' => 2048,
        ],
        'social' => [
            'enabled' => true,
            'max_url_length' => 2048,
        ],
        'event' => [
            'enabled' => true,
            'max_title_length' => 200,
            'max_description_length' => 1000,
        ],
        'location' => [
            'enabled' => true,
            'max_address_length' => 500,
        ],
        'feedback' => [
            'enabled' => true,
            'max_url_length' => 2048,
        ],
        'crypto' => [
            'enabled' => true,
            'max_address_length' => 100,
        ],
    ],

    'design' => [
        'default_colors' => [
            'foreground' => '#000000',
            'background' => '#ffffff',
            'eye' => '#000000',
        ],
        'max_logo_size' => env('QR_CODE_MAX_LOGO_SIZE', 100), // pixels
        'max_sticker_length' => env('QR_CODE_MAX_STICKER_LENGTH', 100),
        'allowed_logo_formats' => explode(',', env('QR_CODE_ALLOWED_LOGO_FORMATS', 'png,jpg,jpeg,gif,webp,svg')),
    ],

    'analytics' => [
        'enabled' => env('QR_CODE_ANALYTICS_ENABLED', true),
        'track_scans' => env('QR_CODE_TRACK_SCANS', true),
        'track_locations' => env('QR_CODE_TRACK_LOCATIONS', true),
        'track_devices' => env('QR_CODE_TRACK_DEVICES', true),
        'retention_days' => env('QR_CODE_ANALYTICS_RETENTION_DAYS', 365),
    ],

    'cleanup' => [
        'enabled' => env('QR_CODE_CLEANUP_ENABLED', true),
        'delete_after_days' => env('QR_CODE_DELETE_AFTER_DAYS', 30),
        'cleanup_frequency' => env('QR_CODE_CLEANUP_FREQUENCY', 'daily'),
    ],
];
