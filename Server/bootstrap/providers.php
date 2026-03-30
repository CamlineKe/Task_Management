<?php

use App\Providers\AppServiceProvider;
use App\Providers\RepositoryServiceProvider;

/**
 * Service Providers Bootstrap
 * Auto-discovered providers for Laravel 11 application
 */

return [
    # Framework foundation provider
    \Illuminate\Foundation\Providers\FoundationServiceProvider::class,

    # Application providers
    AppServiceProvider::class,

    # Custom repository and service bindings
    RepositoryServiceProvider::class,
];