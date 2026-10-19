<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'same-origin');
        $response->headers->set('Cache-Control', 'no-store');

        $cdn = 'https://cdnjs.cloudflare.com';
        $response->headers->set('Content-Security-Policy', implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' $cdn",
            "style-src 'self' 'unsafe-inline' $cdn",
            "img-src 'self' data: $cdn",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]));

        return $response;
    }
}
