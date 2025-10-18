<?php

namespace App\Console\Commands;

use App\Jobs\SendWhatsAppNotification;
use App\Models\User;
use Illuminate\Console\Command;

class SendTrialExpiringNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:trial-expiring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send WhatsApp notifications to users whose trial is expiring';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando envio de notificações de trial expirando...');

        $daysToCheck = config('whatsapp.notifications.trial_expiring.days_before', [3, 1]);
        $totalSent = 0;

        foreach ($daysToCheck as $days) {
            $this->info("Verificando usuários com trial expirando em {$days} dia(s)...");

            // Buscar usuários com trial expirando em X dias
            $users = User::where('subscription_status', 'trialing')
                ->where('trial_ends_at', '<=', now()->addDays($days))
                ->where('trial_ends_at', '>', now()->addDays($days - 1))
                ->whereNotNull('phone')
                ->get();

            $this->info("Encontrados {$users->count()} usuários com trial expirando em {$days} dia(s)");

            foreach ($users as $user) {
                // Verificar se já foi enviada notificação para este período
                $notificationKey = "trial_expiring_{$days}d_{$user->id}";
                
                if (cache()->has($notificationKey)) {
                    $this->line("  - Pulando {$user->email} (já notificado)");
                    continue;
                }

                // Enviar notificação
                SendWhatsAppNotification::dispatch($user, 'trial_expiring', [
                    'days_left' => $days,
                ]);

                // Marcar como enviado (válido por 2 dias)
                cache()->put($notificationKey, true, now()->addDays(2));

                $this->line("  - Notificação enviada para {$user->email}");
                $totalSent++;
            }
        }

        $this->info("Notificações enviadas: {$totalSent}");
        $this->info('Processo concluído!');

        return 0;
    }
}