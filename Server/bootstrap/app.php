<?php

use App\Http\Middleware\Cors;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

/**
 * Application Bootstrap
 * Configures Laravel 11 application with custom middleware
 */

return Application::configure(basePath: dirname(__DIR__))
    # Service providers are auto-discovered via bootstrap/providers.php
    ->withProviders()

    # Routing configuration - includes API routes
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    # Middleware configuration
    ->withMiddleware(function (Middleware $middleware): void {
        # Register custom middleware aliases for route-specific use
        $middleware->alias([
            'cors' => Cors::class,
            'force.json' => ForceJsonResponse::class,
        ]);

        # Prepend global middleware to all routes
        # ForceJsonResponse ensures all API responses are JSON format
        # Cors allows Vue.js frontend communication
        $middleware->prependToGroup('api', [
            ForceJsonResponse::class,
            Cors::class,
        ]);
    })

    # Exception handling configuration
    ->withExceptions(function (Exceptions $exceptions): void {
        # Handle validation exceptions - return 422 with errors
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        # Render JSON exceptions for API requests
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500);
            }
        });
    })->create();