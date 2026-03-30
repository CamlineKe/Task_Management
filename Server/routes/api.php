<?php

use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\Route;

/**
 * API Routes
 * Versioned API endpoints for task management system
 * All routes prefixed with /api/ automatically
 */

Route::prefix('v1')->group(function () {
    # CRITICAL: Report route MUST come BEFORE apiResource
    # Otherwise /tasks/report matches tasks/{id} show route pattern
    Route::get('tasks/report', [ReportController::class, 'daily']);

    # Task resource routes - only index, store, destroy
    # Note: show, update are excluded per requirements
    Route::apiResource('tasks', TaskController::class)
        ->only(['index', 'store', 'destroy']);

    # Status update endpoint - custom PATCH route
    # Not part of standard resource routes
    Route::patch('tasks/{id}/status', [TaskController::class, 'updateStatus']);
});