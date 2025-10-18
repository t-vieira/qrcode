<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class RateLimitingService
{
    /**
     * Verificar rate limiting para uma ação específica
     */
    public function checkRateLimit(Request $request, string $action, ?User $user = null): bool
    {
        $key = $this->generateKey($request, $action, $user);
        $config = $this->getConfigForAction($action);
        
        if (!$config) {
            return true; // Sem limite configurado
        }

        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $config['max_attempts']) {
            $this->logRateLimitExceeded($request, $action, $attempts, $user);
            return false;
        }

        Cache::put($key, $attempts + 1, $config['decay_minutes'] * 60);
        return true;
    }

    /**
     * Verificar rate limiting baseado no tipo de usuário
     */
    public function checkUserTypeRateLimit(User $user, string $action): bool
    {
        $userType = $this->getUserType($user);
        $limits = config("rate_limiting.user_limits.{$userType}");
        
        if (!$limits || !isset($limits[$action])) {
            return true;
        }

        $key = "user_limit:{$user->id}:{$action}";
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $limits[$action]) {
            $this->logUserRateLimitExceeded($user, $action, $attempts);
            return false;
        }

        // Incrementar contador baseado no tipo de ação
        $decayMinutes = $this->getDecayMinutesForAction($action);
        Cache::put($key, $attempts + 1, $decayMinutes * 60);
        
        return true;
    }

    /**
     * Verificar rate limiting baseado em IP
     */
    public function checkIpRateLimit(Request $request): bool
    {
        $ip = $request->ip();
        
        // Verificar se IP está em exceções
        if ($this->isIpExcepted($ip)) {
            return true;
        }

        // Verificar se IP está bloqueado
        if ($this->isIpBlocked($ip)) {
            return false;
        }

        // Verificar se IP é suspeito
        $ipType = $this->getIpType($ip);
        $config = config("rate_limiting.ip_limits.{$ipType}");
        
        if (!$config) {
            $config = config('rate_limiting.ip_limits.general');
        }

        $key = "ip_limit:{$ip}";
        $attempts = Cache::get($key, 0);
        
        if ($attempts >= $config['max_requests']) {
            $this->logIpRateLimitExceeded($ip, $attempts);
            return false;
        }

        Cache::put($key, $attempts + 1, $config['decay_minutes'] * 60);
        return true;
    }

    /**
     * Obter tempo restante para rate limit
     */
    public function getRemainingTime(Request $request, string $action, ?User $user = null): int
    {
        $key = $this->generateKey($request, $action, $user);
        $config = $this->getConfigForAction($action);
        
        if (!$config) {
            return 0;
        }

        $ttl = Cache::get($key . ':ttl', 0);
        return max(0, $ttl - time());
    }

    /**
     * Obter número de tentativas restantes
     */
    public function getRemainingAttempts(Request $request, string $action, ?User $user = null): int
    {
        $key = $this->generateKey($request, $action, $user);
        $config = $this->getConfigForAction($action);
        
        if (!$config) {
            return 999;
        }

        $attempts = Cache::get($key, 0);
        return max(0, $config['max_attempts'] - $attempts);
    }

    /**
     * Resetar rate limit para um usuário específico
     */
    public function resetRateLimit(Request $request, string $action, ?User $user = null): void
    {
        $key = $this->generateKey($request, $action, $user);
        Cache::forget($key);
        Cache::forget($key . ':ttl');
    }

    /**
     * Marcar IP como suspeito
     */
    public function markIpAsSuspicious(string $ip): void
    {
        Cache::put("suspicious_ip:{$ip}", true, 3600); // 1 hora
        Log::warning('IP marked as suspicious', ['ip' => $ip]);
    }

    /**
     * Marcar IP como bloqueado
     */
    public function markIpAsBlocked(string $ip, int $minutes = 60): void
    {
        Cache::put("blocked_ip:{$ip}", true, $minutes * 60);
        Log::warning('IP marked as blocked', ['ip' => $ip, 'minutes' => $minutes]);
    }

    /**
     * Obter estatísticas de rate limiting
     */
    public function getRateLimitStats(): array
    {
        return [
            'blocked_ips' => $this->getBlockedIpsCount(),
            'suspicious_ips' => $this->getSuspiciousIpsCount(),
            'rate_limited_actions' => $this->getRateLimitedActionsCount(),
        ];
    }

    /**
     * Gerar chave única para rate limiting
     */
    private function generateKey(Request $request, string $action, ?User $user = null): string
    {
        $ip = $request->ip();
        $userKey = $user ? $user->id : 'guest';
        
        return "rate_limit:{$action}:{$ip}:{$userKey}";
    }

    /**
     * Obter configuração para uma ação específica
     */
    private function getConfigForAction(string $action): ?array
    {
        $config = config('rate_limiting.limits');
        
        // Buscar configuração em diferentes seções
        foreach ($config as $section => $limits) {
            if (isset($limits[$action])) {
                return $limits[$action];
            }
        }
        
        return null;
    }

    /**
     * Obter tipo de usuário
     */
    private function getUserType(User $user): string
    {
        if ($user->hasRole('admin')) {
            return 'admin';
        }
        
        if ($user->hasActiveSubscription()) {
            return 'premium';
        }
        
        return 'trial';
    }

    /**
     * Verificar se IP está em exceções
     */
    private function isIpExcepted(string $ip): bool
    {
        $exceptions = config('rate_limiting.exceptions.ips', []);
        return in_array($ip, $exceptions);
    }

    /**
     * Verificar se IP está bloqueado
     */
    private function isIpBlocked(string $ip): bool
    {
        return Cache::has("blocked_ip:{$ip}");
    }

    /**
     * Obter tipo de IP
     */
    private function getIpType(string $ip): string
    {
        if (Cache::has("blocked_ip:{$ip}")) {
            return 'blocked';
        }
        
        if (Cache::has("suspicious_ip:{$ip}")) {
            return 'suspicious';
        }
        
        return 'general';
    }

    /**
     * Obter minutos de decay para uma ação
     */
    private function getDecayMinutesForAction(string $action): int
    {
        $decayMap = [
            'qr_codes_per_hour' => 60,
            'scans_per_hour' => 60,
            'exports_per_day' => 1440, // 24 horas
        ];
        
        return $decayMap[$action] ?? 60;
    }

    /**
     * Log de rate limit excedido
     */
    private function logRateLimitExceeded(Request $request, string $action, int $attempts, ?User $user = null): void
    {
        Log::warning('Rate limit exceeded', [
            'action' => $action,
            'attempts' => $attempts,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => $user?->id,
            'user_email' => $user?->email,
        ]);
    }

    /**
     * Log de rate limit de usuário excedido
     */
    private function logUserRateLimitExceeded(User $user, string $action, int $attempts): void
    {
        Log::warning('User rate limit exceeded', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'action' => $action,
            'attempts' => $attempts,
        ]);
    }

    /**
     * Log de rate limit de IP excedido
     */
    private function logIpRateLimitExceeded(string $ip, int $attempts): void
    {
        Log::warning('IP rate limit exceeded', [
            'ip' => $ip,
            'attempts' => $attempts,
        ]);
    }

    /**
     * Obter número de IPs bloqueados
     */
    private function getBlockedIpsCount(): int
    {
        // Implementar lógica para contar IPs bloqueados
        return 0;
    }

    /**
     * Obter número de IPs suspeitos
     */
    private function getSuspiciousIpsCount(): int
    {
        // Implementar lógica para contar IPs suspeitos
        return 0;
    }

    /**
     * Obter número de ações com rate limit
     */
    private function getRateLimitedActionsCount(): int
    {
        // Implementar lógica para contar ações com rate limit
        return 0;
    }
}
