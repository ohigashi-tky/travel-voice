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

        // Set CORS headers - Allow all origins including specific Railway frontend
        $origin = $request->headers->get('Origin');
        if ($origin === 'https://travel-voice-production-61af.up.railway.app' || 
            $origin === 'https://travel-voice-production-61af.up.railway.app:8080' ||
            str_contains($origin, 'localhost') || 
            str_contains($origin, 'railway.app')) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
        } else {
            $response->headers->set('Access-Control-Allow-Origin', '*');
        }
        
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, Accept, Origin, X-CSRF-TOKEN');
        $response->headers->set('Access-Control-Allow-Credentials', 'false');
        $response->headers->set('Access-Control-Max-Age', '86400');

        return $response;
    }
}
