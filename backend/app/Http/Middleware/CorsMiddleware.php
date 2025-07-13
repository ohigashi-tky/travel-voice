<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');
        
        // Handle preflight OPTIONS requests
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 200);
        } else {
            $response = $next($request);
        }

        // Set CORS headers
        if ($origin) {
            $allowedOrigins = [
                'http://localhost:3000',
                'https://travel-voice-production.up.railway.app',
                'https://travel-voice-production-61af.up.railway.app'
            ];
            
            if (in_array($origin, $allowedOrigins) || 
                str_contains($origin, 'railway.app') ||
                str_contains($origin, 'localhost')) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
            } else {
                $response->headers->set('Access-Control-Allow-Origin', '*');
            }
        } else {
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }
        
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-CSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Max-Age', '86400');

        return $response;
    }
}
