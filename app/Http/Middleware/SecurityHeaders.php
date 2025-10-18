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

        // Verificar se CSP está habilitado
        if (!config('security.csp.enabled', true)) {
            return $response;
        }

        // Security Headers
        $headers = config('security.headers', []);
        
        if (isset($headers['x_content_type_options'])) {
            $response->headers->set('X-Content-Type-Options', $headers['x_content_type_options']);
        }
        if (isset($headers['x_frame_options'])) {
            $response->headers->set('X-Frame-Options', $headers['x_frame_options']);
        }
        if (isset($headers['x_xss_protection'])) {
            $response->headers->set('X-XSS-Protection', $headers['x_xss_protection']);
        }
        if (isset($headers['referrer_policy'])) {
            $response->headers->set('Referrer-Policy', $headers['referrer_policy']);
        }
        if (isset($headers['permissions_policy'])) {
            $response->headers->set('Permissions-Policy', $headers['permissions_policy']);
        }
        
        // Content Security Policy
        $csp = $this->buildCSP($request);
        if (config('security.csp.report_only', false)) {
            $response->headers->set('Content-Security-Policy-Report-Only', $csp);
        } else {
            $response->headers->set('Content-Security-Policy', $csp);
        }
        
        // Strict Transport Security (apenas em HTTPS)
        if ($request->isSecure() && config('security.headers.strict_transport_security', true)) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        return $response;
    }

    /**
     * Build Content Security Policy
     */
    private function buildCSP(Request $request): string
    {
        $directives = config('security.csp_directives', []);

        $cspString = '';
        foreach ($directives as $directive => $sources) {
            $cspString .= $directive . ' ' . implode(' ', $sources) . '; ';
        }

        // Adicionar report-uri se configurado
        if (config('security.csp.report_uri')) {
            $cspString .= 'report-uri ' . config('security.csp.report_uri') . '; ';
        }

        return trim($cspString);
    }
}