<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\User;

class SecurityAuditService
{
    /**
     * Log de ações críticas do usuário
     */
    public function logCriticalAction(User $user, string $action, array $data = []): void
    {
        Log::channel('security')->info('Critical user action', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'action' => $action,
            'data' => $data,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log de tentativas de login
     */
    public function logLoginAttempt(string $email, bool $success, ?string $reason = null): void
    {
        $level = $success ? 'info' : 'warning';
        
        Log::channel('security')->$level('Login attempt', [
            'email' => $email,
            'success' => $success,
            'reason' => $reason,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);

        // Incrementar contador de tentativas falhadas
        if (!$success) {
            $this->incrementFailedLoginAttempts($email);
        }
    }

    /**
     * Log de mudanças de senha
     */
    public function logPasswordChange(User $user, bool $success): void
    {
        $level = $success ? 'info' : 'warning';
        
        Log::channel('security')->$level('Password change attempt', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'success' => $success,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log de mudanças de assinatura
     */
    public function logSubscriptionChange(User $user, string $action, array $data = []): void
    {
        Log::channel('security')->info('Subscription change', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'action' => $action,
            'data' => $data,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log de criação de QR Code
     */
    public function logQrCodeCreation(User $user, array $qrCodeData): void
    {
        Log::channel('security')->info('QR Code created', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'qr_code_type' => $qrCodeData['type'] ?? null,
            'is_dynamic' => $qrCodeData['is_dynamic'] ?? false,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log de acesso a dados sensíveis
     */
    public function logSensitiveDataAccess(User $user, string $dataType, array $data = []): void
    {
        Log::channel('security')->info('Sensitive data access', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'data_type' => $dataType,
            'data' => $data,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log de atividade suspeita
     */
    public function logSuspiciousActivity(string $type, array $data = []): void
    {
        Log::channel('security')->warning('Suspicious activity detected', [
            'type' => $type,
            'data' => $data,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log de falhas de pagamento
     */
    public function logPaymentFailure(User $user, string $reason, array $data = []): void
    {
        Log::channel('security')->warning('Payment failure', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'reason' => $reason,
            'data' => $data,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log de webhook recebido
     */
    public function logWebhookReceived(string $source, array $data = []): void
    {
        Log::channel('security')->info('Webhook received', [
            'source' => $source,
            'data' => $data,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Incrementar tentativas de login falhadas
     */
    private function incrementFailedLoginAttempts(string $email): void
    {
        $key = "failed_login_attempts:{$email}";
        $attempts = Cache::get($key, 0) + 1;
        
        Cache::put($key, $attempts, 3600); // 1 hora

        // Se muitas tentativas, bloquear temporariamente
        if ($attempts >= 5) {
            $this->temporarilyBlockEmail($email);
        }
    }

    /**
     * Bloquear email temporariamente
     */
    private function temporarilyBlockEmail(string $email): void
    {
        $key = "blocked_email:{$email}";
        Cache::put($key, true, 3600); // 1 hora

        Log::channel('security')->warning('Email temporarily blocked due to failed login attempts', [
            'email' => $email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Verificar se email está bloqueado
     */
    public function isEmailBlocked(string $email): bool
    {
        return Cache::has("blocked_email:{$email}");
    }

    /**
     * Obter estatísticas de segurança
     */
    public function getSecurityStats(): array
    {
        return [
            'failed_login_attempts_today' => $this->getFailedLoginAttemptsToday(),
            'blocked_ips_count' => $this->getBlockedIpsCount(),
            'suspicious_activities_today' => $this->getSuspiciousActivitiesToday(),
            'webhook_attempts_today' => $this->getWebhookAttemptsToday(),
        ];
    }

    /**
     * Obter tentativas de login falhadas hoje
     */
    private function getFailedLoginAttemptsToday(): int
    {
        // Implementar lógica para contar tentativas falhadas do dia
        return 0;
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
     * Obter atividades suspeitas hoje
     */
    private function getSuspiciousActivitiesToday(): int
    {
        // Implementar lógica para contar atividades suspeitas do dia
        return 0;
    }

    /**
     * Obter tentativas de webhook hoje
     */
    private function getWebhookAttemptsToday(): int
    {
        // Implementar lógica para contar tentativas de webhook do dia
        return 0;
    }

    /**
     * Limpar logs antigos
     */
    public function cleanOldLogs(int $daysOld = 30): void
    {
        $logPath = storage_path('logs/security.log');
        
        if (file_exists($logPath)) {
            $lines = file($logPath);
            $cutoffDate = now()->subDays($daysOld);
            $filteredLines = [];

            foreach ($lines as $line) {
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    $logDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $matches[1]);
                    if ($logDate->isAfter($cutoffDate)) {
                        $filteredLines[] = $line;
                    }
                }
            }

            file_put_contents($logPath, implode('', $filteredLines));
        }
    }

    /**
     * Gerar relatório de segurança
     */
    public function generateSecurityReport(): array
    {
        return [
            'period' => [
                'start' => now()->subDays(7)->toDateString(),
                'end' => now()->toDateString(),
            ],
            'stats' => $this->getSecurityStats(),
            'recommendations' => $this->getSecurityRecommendations(),
        ];
    }

    /**
     * Obter recomendações de segurança
     */
    private function getSecurityRecommendations(): array
    {
        $recommendations = [];

        $stats = $this->getSecurityStats();

        if ($stats['failed_login_attempts_today'] > 50) {
            $recommendations[] = 'Alto número de tentativas de login falhadas. Considere implementar CAPTCHA adicional.';
        }

        if ($stats['blocked_ips_count'] > 10) {
            $recommendations[] = 'Muitos IPs bloqueados. Considere implementar proteção DDoS.';
        }

        if ($stats['suspicious_activities_today'] > 20) {
            $recommendations[] = 'Alto número de atividades suspeitas. Revise logs de segurança.';
        }

        return $recommendations;
    }
}
