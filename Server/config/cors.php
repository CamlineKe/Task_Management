<?php

/**
 * CORS Configuration
 * Cross-Origin Resource Sharing settings for Vue.js frontend
 */

return [
    # Allowed origins - set to frontend URL in production
    # '*' allows all origins (development only)
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    # Allowed HTTP methods
    'allowed_methods' => ['*'],

    # Allowed request origins
    'allowed_origins' => ['*'],

    # Allowed origin patterns (regex)
    'allowed_origins_patterns' => [],

    # Allowed request headers
    'allowed_headers' => ['*'],

    # Exposed response headers
    'exposed_headers' => [],

    # Max age for preflight cache (seconds)
    'max_age' => 0,

    # Allow credentials (cookies, authorization headers)
    'supports_credentials' => true,
];