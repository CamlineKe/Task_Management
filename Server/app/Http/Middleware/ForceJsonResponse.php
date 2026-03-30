<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * ForceJsonResponse Middleware
 * Forces all API responses to be JSON format
 * Ensures consistent content-type across all endpoints
 */
class ForceJsonResponse
{
    /**
     * Handle incoming request
     * Sets Accept header to application/json to force JSON responses
     *
     * @param Request $request Incoming request
     * @param Closure $next Next middleware in stack
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        # Force JSON accept header for all requests through this middleware
        $request->headers->set('Accept', 'application/json');

        # Process request
        $response = $next($request);

        # Ensure response has JSON content type
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}