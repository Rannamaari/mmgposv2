<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogPOSActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $duration = (microtime(true) - $startTime) * 1000;

        // Log POS activities
        if ($request->is('admin/pos*') || $request->is('pos*')) {
            Log::channel('pos')->info('POS Activity', [
                'user_id' => auth()->id(),
                'user_name' => auth()->user()?->name,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'duration_ms' => round($duration, 2),
                'memory_usage' => memory_get_peak_usage(true),
                'response_status' => $response->getStatusCode(),
            ]);
        }

        // Log slow requests
        if ($duration > 2000) { // 2 seconds
            Log::channel('performance')->warning('Slow Request', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'duration_ms' => round($duration, 2),
                'memory_usage' => memory_get_peak_usage(true),
                'user_id' => auth()->id(),
            ]);
        }

        // Log failed requests
        if ($response->getStatusCode() >= 400) {
            Log::channel('security')->warning('Failed Request', [
                'status' => $response->getStatusCode(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'duration_ms' => round($duration, 2),
            ]);
        }

        return $response;
    }
}