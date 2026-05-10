<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configured for V-Mart VR Store:
    |   - Unity (WebGL / Android builds) — needs wildcard origin
    |   - Android Studio (Retrofit / OkHttp) — sends standard headers
    |   - Cloudflare Quick Tunnel — HTTPS tunnel, origin varies per session
    |
    | IMPORTANT: For production, replace 'allowed_origins' => ['*'] with your
    | specific Cloudflare tunnel domain or permanent domain name.
    |
    */

    // Cover every API route AND the Sanctum CSRF cookie endpoint
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    // Allow GET, POST, PUT, DELETE, OPTIONS (preflight), PATCH
    'allowed_methods' => ['*'],

    // Allow any origin — Unity WebGL builds and Android both work
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    // Allow Content-Type, Authorization (Bearer token), Accept, X-Requested-With
    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization'],

    // Cache preflight for 24 hours to reduce OPTIONS round-trips from Unity
    'max_age' => 86400,

    // Must be false when allowed_origins is '*' (browser restriction)
    'supports_credentials' => false,

];
