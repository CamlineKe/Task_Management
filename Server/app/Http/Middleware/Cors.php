<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cors Middleware
 * Handles Cross-Origin Resource Sharing for Vue.js frontend
 * Allows requests from frontend development server
 */
class Cors
{
    /**
     * Handle incoming request
     * Adds CORS headers to allow frontend communication
     *
     * @param Request $request Incoming request
     * @param Closure $next Next middleware in stack
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        # Handle preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        # Add CORS headers to allow Vue.js frontend
        # Adjust origin as needed for production
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}