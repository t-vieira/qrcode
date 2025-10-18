<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ExpireTrialSubscriptions implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Iniciando expiração de trials');

            // Buscar usuários com trial expirado
            $expiredUsers = User::where('subscription_status', 'trialing')
                ->where('trial_ends_at', '<=', now())
                ->get();

            $count = 0;

            foreach ($expiredUsers as $user) {
                $user->update([
                    'subscription_status' => 'expired',
                ]);

                Log::info("Trial expirado para usuário: {$user->id} ({$user->email})");
                $count++;
            }

            Log::info("Expiração de trials concluída. {$count} usuários afetados.");

        } catch (\Exception $e) {
            Log::error('Erro ao expirar trials: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de expiração de trials falhou', [
            'exception' => $exception,
        ]);
    }
}