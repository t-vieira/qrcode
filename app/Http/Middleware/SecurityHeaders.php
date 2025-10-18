<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security Headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
        
        // Content Security Policy
        $csp = $this->buildCSP($request);
        $response->headers->set('Content-Security-Policy', $csp);
        
        // Strict Transport Security (apenas em HTTPS)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }

    /**
     * Build Content Security Policy
     */
    private function buildCSP(Request $request): string
    {
        $directives = [
            'default-src' => ["'self'"],
            'script-src' => [
                "'self'",
                "'unsafe-inline'", // Para Alpine.js
                "'unsafe-eval'", // Para Chart.js
                'https://www.google.com',
                'https://www.gstatic.com',
                'https://www.google-analytics.com',
                'https://www.googletagmanager.com',
                'https://cdn.jsdelivr.net',
                'https://unpkg.com',
            ],
            'style-src' => [
                "'self'",
                "'unsafe-inline'", // Para Tailwind CSS
                'https://fonts.googleapis.com',
            ],
            'font-src' => [
                "'self'",
                'https://fonts.gstatic.com',
                'data:',
            ],
            'img-src' => [
                "'self'",
                'data:',
                'blob:',
                'https:',
            ],
            'connect-src' => [
                "'self'",
                'https://api.mercadopago.com',
                'https://graph.facebook.com',
                'https://api.whatsapp.com',
                'https://ipapi.co',
                'https://ip-api.com',
            ],
            'media-src' => [
                "'self'",
                'data:',
                'blob:',
            ],
            'object-src' => ["'none'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'none'"],
        ];

        $cspString = '';
        foreach ($directives as $directive => $sources) {
            $cspString .= $directive . ' ' . implode(' ', $sources) . '; ';
        }

        return trim($cspString);
    }
}