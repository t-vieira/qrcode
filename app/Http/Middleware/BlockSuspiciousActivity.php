<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BlockSuspiciousActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $path = $request->path();

        // Verificar se o IP está bloqueado
        if ($this->isIpBlocked($ip)) {
            Log::warning('Blocked IP attempt', [
                'ip' => $ip,
                'path' => $path,
                'user_agent' => $userAgent,
            ]);
            abort(403, 'Acesso negado.');
        }

        // Verificar atividade suspeita
        if ($this->isSuspiciousActivity($request)) {
            $this->blockIp($ip);
            Log::critical('Suspicious activity detected and IP blocked', [
                'ip' => $ip,
                'path' => $path,
                'user_agent' => $userAgent,
                'request_data' => $request->all(),
            ]);
            abort(403, 'Atividade suspeita detectada.');
        }

        // Verificar rate limiting personalizado
        if ($this->isRateLimited($request)) {
            Log::warning('Rate limit exceeded', [
                'ip' => $ip,
                'path' => $path,
                'user_agent' => $userAgent,
            ]);
            abort(429, 'Muitas tentativas. Tente novamente mais tarde.');
        }

        return $next($request);
    }

    /**
     * Verificar se o IP está bloqueado
     */
    private function isIpBlocked(string $ip): bool
    {
        return Cache::has("blocked_ip:{$ip}");
    }

    /**
     * Verificar atividade suspeita
     */
    private function isSuspiciousActivity(Request $request): bool
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $path = $request->path();

        // Verificar User-Agent suspeito
        if ($this->isSuspiciousUserAgent($userAgent)) {
            return true;
        }

        // Verificar tentativas de acesso a arquivos sensíveis
        if ($this->isAccessingSensitiveFiles($path)) {
            return true;
        }

        // Verificar tentativas de SQL injection
        if ($this->hasSqlInjectionAttempts($request)) {
            return true;
        }

        // Verificar tentativas de XSS
        if ($this->hasXssAttempts($request)) {
            return true;
        }

        // Verificar tentativas de path traversal
        if ($this->hasPathTraversalAttempts($request)) {
            return true;
        }

        // Verificar muitas requisições em pouco tempo
        if ($this->hasTooManyRequests($ip)) {
            return true;
        }

        return false;
    }

    /**
     * Verificar User-Agent suspeito
     */
    private function isSuspiciousUserAgent(?string $userAgent): bool
    {
        if (!$userAgent) {
            return true;
        }

        $suspiciousPatterns = [
            'sqlmap',
            'nikto',
            'nmap',
            'masscan',
            'zap',
            'burp',
            'w3af',
            'acunetix',
            'nessus',
            'openvas',
            'curl',
            'wget',
            'python-requests',
            'bot',
            'crawler',
            'spider',
            'scanner',
            'hack',
            'exploit',
        ];

        $userAgentLower = strtolower($userAgent);

        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($userAgentLower, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar acesso a arquivos sensíveis
     */
    private function isAccessingSensitiveFiles(string $path): bool
    {
        $sensitivePaths = [
            '.env',
            'config/database.php',
            'storage/logs/',
            'vendor/',
            'composer.json',
            'package.json',
            'webpack.mix.js',
            'artisan',
            'phpinfo',
            'info.php',
            'test.php',
            'admin',
            'wp-admin',
            'wp-login',
            'administrator',
            'phpmyadmin',
            'mysql',
            'sql',
            'backup',
            'backups',
        ];

        foreach ($sensitivePaths as $sensitivePath) {
            if (strpos($path, $sensitivePath) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar tentativas de SQL injection
     */
    private function hasSqlInjectionAttempts(Request $request): bool
    {
        $sqlPatterns = [
            'union select',
            'drop table',
            'delete from',
            'insert into',
            'update set',
            'alter table',
            'create table',
            'exec(',
            'execute(',
            'sp_',
            'xp_',
            'information_schema',
            'mysql.user',
            'pg_user',
            'or 1=1',
            'and 1=1',
            'or 1=0',
            'and 1=0',
            'or true',
            'and true',
            'or false',
            'and false',
            'sleep(',
            'waitfor delay',
            'benchmark(',
            'load_file(',
            'into outfile',
            'into dumpfile',
        ];

        $allData = $request->all();
        $allDataString = json_encode($allData);

        foreach ($sqlPatterns as $pattern) {
            if (stripos($allDataString, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar tentativas de XSS
     */
    private function hasXssAttempts(Request $request): bool
    {
        $xssPatterns = [
            '<script',
            '</script>',
            'javascript:',
            'vbscript:',
            'onload=',
            'onerror=',
            'onclick=',
            'onmouseover=',
            'onfocus=',
            'onblur=',
            'onchange=',
            'onsubmit=',
            'onreset=',
            'onkeydown=',
            'onkeyup=',
            'onkeypress=',
            'onmousedown=',
            'onmouseup=',
            'onmousemove=',
            'onmouseout=',
            'onmouseenter=',
            'onmouseleave=',
            'oncontextmenu=',
            'onabort=',
            'onbeforeunload=',
            'onerror=',
            'onhashchange=',
            'onload=',
            'onpageshow=',
            'onpagehide=',
            'onresize=',
            'onscroll=',
            'onunload=',
            'eval(',
            'expression(',
            'url(',
            'behavior:',
            'binding:',
            '-moz-binding',
            'expression(',
            'javascript:',
            'vbscript:',
            'data:text/html',
            'data:application/javascript',
        ];

        $allData = $request->all();
        $allDataString = json_encode($allData);

        foreach ($xssPatterns as $pattern) {
            if (stripos($allDataString, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar tentativas de path traversal
     */
    private function hasPathTraversalAttempts(Request $request): bool
    {
        $pathTraversalPatterns = [
            '../',
            '..\\',
            '..%2f',
            '..%5c',
            '%2e%2e%2f',
            '%2e%2e%5c',
            '....//',
            '....\\\\',
            '..%252f',
            '..%255c',
            '%252e%252e%252f',
            '%252e%252e%255c',
        ];

        $allData = $request->all();
        $allDataString = json_encode($allData);

        foreach ($pathTraversalPatterns as $pattern) {
            if (stripos($allDataString, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar muitas requisições em pouco tempo
     */
    private function hasTooManyRequests(string $ip): bool
    {
        $key = "requests:{$ip}";
        $requests = Cache::get($key, 0);
        
        // Permitir 100 requisições por minuto
        if ($requests > 100) {
            return true;
        }

        Cache::put($key, $requests + 1, 60); // 1 minuto
        return false;
    }

    /**
     * Verificar rate limiting personalizado
     */
    private function isRateLimited(Request $request): bool
    {
        $ip = $request->ip();
        $path = $request->path();

        // Rate limiting específico por rota
        $rateLimits = [
            'login' => ['limit' => 5, 'window' => 300], // 5 tentativas em 5 minutos
            'register' => ['limit' => 3, 'window' => 600], // 3 tentativas em 10 minutos
            'password/email' => ['limit' => 3, 'window' => 600], // 3 tentativas em 10 minutos
            'webhook' => ['limit' => 100, 'window' => 60], // 100 tentativas em 1 minuto
        ];

        foreach ($rateLimits as $route => $config) {
            if (strpos($path, $route) !== false) {
                $key = "rate_limit:{$route}:{$ip}";
                $attempts = Cache::get($key, 0);
                
                if ($attempts >= $config['limit']) {
                    return true;
                }

                Cache::put($key, $attempts + 1, $config['window']);
                break;
            }
        }

        return false;
    }

    /**
     * Bloquear IP
     */
    private function blockIp(string $ip): void
    {
        Cache::put("blocked_ip:{$ip}", true, 3600); // Bloquear por 1 hora
    }
}