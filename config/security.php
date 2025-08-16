<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */

    'trusted_proxies' => env('TRUSTED_PROXIES', '*'),
    'trusted_hosts' => array_filter(explode(',', env('TRUSTED_HOSTS', ''))),
    
    'rate_limiting' => [
        'per_minute' => env('RATE_LIMIT_PER_MINUTE', 60),
        'api_per_minute' => env('API_RATE_LIMIT_PER_MINUTE', 100),
    ],

    'file_uploads' => [
        'max_size' => env('FILAMENT_MAX_UPLOAD_SIZE', 10240), // KB
        'allowed_mimes' => ['jpg', 'jpeg', 'png', 'pdf'],
    ],

    'session' => [
        'secure_cookies' => env('SESSION_SECURE_COOKIE', true),
        'same_site' => env('SESSION_SAME_SITE', 'lax'),
        'encrypt' => env('SESSION_ENCRYPT', true),
    ],

    'headers' => [
        'hsts' => env('SECURITY_HSTS', true),
        'content_type_options' => env('SECURITY_CONTENT_TYPE_OPTIONS', true),
        'frame_options' => env('SECURITY_FRAME_OPTIONS', 'DENY'),
        'xss_protection' => env('SECURITY_XSS_PROTECTION', true),
        'referrer_policy' => env('SECURITY_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
    ],
];