<?php

namespace App\Providers;

use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Repositories\TaskRepository;
use App\Services\Interfaces\ReportServiceInterface;
use App\Services\Interfaces\TaskServiceInterface;
use App\Services\ReportService;
use App\Services\TaskService;
use Illuminate\Support\ServiceProvider;

/**
 * RepositoryServiceProvider
 * Binds interfaces to concrete implementations for dependency injection
 * Centralizes all repository and service bindings
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services
     * Binds interfaces to implementations in the container
     */
    public function register(): void
    {
        # Repository bindings - data access layer
        $this->app->bind(
            TaskRepositoryInterface::class,
            TaskRepository::class
        );

        # Service bindings - business logic layer
        $this->app->bind(
            TaskServiceInterface::class,
            TaskService::class
        );

        $this->app->bind(
            ReportServiceInterface::class,
            ReportService::class
        );
    }

    /**
     * Bootstrap services
     * Called after all providers registered
     */
    public function boot(): void
    {
        # No boot actions needed
    }
}