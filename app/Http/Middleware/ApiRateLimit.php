<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Simple rate limiting untuk API
        $clientIp = $request->ip();
        $cacheKey = 'rate_limit:' . $clientIp;
        $maxRequests = 100; // Max requests per minute
        $timeWindow = 60; // seconds

        $currentRequests = cache()->get($cacheKey, 0);
        
        if ($currentRequests >= $maxRequests) {
            return response()->json([
                'error' => 'Rate limit exceeded. Please try again later.',
                'retry_after' => $timeWindow
            ], 429);
        }

        cache()->put($cacheKey, $currentRequests + 1, $timeWindow);

        $response = $next($request);
        
        // Add rate limit headers
        $response->headers->set('X-RateLimit-Limit', $maxRequests);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxRequests - $currentRequests - 1));

        return $response;
    }
}
