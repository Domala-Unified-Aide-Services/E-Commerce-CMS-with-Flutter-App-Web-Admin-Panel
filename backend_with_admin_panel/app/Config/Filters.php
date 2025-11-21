<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Filters extends BaseConfig
{
    /*
    |--------------------------------------------------------------------------
    | Filter Aliases
    |--------------------------------------------------------------------------
    |
    | Aliases make it simpler to use filters without needing to write out
    | the full class name each time.
    |
    */

    public array $aliases = [
        'csrf'        => \CodeIgniter\Filters\CSRF::class,
        'toolbar'     => \CodeIgniter\Filters\DebugToolbar::class,
        'honeypot'    => \CodeIgniter\Filters\Honeypot::class,
        'invalidchars'=> \CodeIgniter\Filters\InvalidChars::class,
        'secureheaders'=> \CodeIgniter\Filters\SecureHeaders::class,
        'adminAuth' => \App\Filters\AdminAuth::class,
        'jwt' => \App\Filters\JwtAuthFilter::class,
    
        // ✅ Add this line
        'cors'        => \App\Filters\Cors::class,
        'cors' => \App\Filters\CorsFilter::class,

    ];
    
    /*
    |--------------------------------------------------------------------------
    | Global Filters
    |--------------------------------------------------------------------------
    |
    | Filters that run on every request.
    | We keep these empty for API use.
    |
    */
    public array $globals = [
        'before' => [
            'cors',    // ✅ Allow cross-origin API requests
            //'csrf',
        ],
        'after' => [
            'toolbar',
        ],
    ];
    
    /*
    |--------------------------------------------------------------------------
    | Method Filters
    |--------------------------------------------------------------------------
    |
    | Filters that run on specific HTTP methods.
    |
    */

    public $methods = [];

    /*
    |--------------------------------------------------------------------------
    | URI Filters
    |--------------------------------------------------------------------------
    |
    | Filters that apply to specific route groups.
    | Not used here because we control auth in Routes.php directly.
    |
    */

    public array $filters = [
    'jwt' => [
        'before' => [
            'api/orders/*',
            'api/orders',
            'api/products/*',
            'api/cart/*',
            'api/auth/me',
        ]
    ]
];
}
