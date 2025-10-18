<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListAdminUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listar todos os usuários administradores';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('👥 Listando usuários administradores...');
        $this->line('');

        // Buscar usuários com status active (considerados admin)
        $admins = User::where('subscription_status', 'active')
            ->whereNull('trial_ends_at')
            ->get();

        if ($admins->isEmpty()) {
            $this->warn('⚠️ Nenhum usuário administrador encontrado.');
            $this->line('');
            $this->info('💡 Para criar um admin, execute:');
            $this->line('   php artisan admin:create');
            return Command::SUCCESS;
        }

        $this->info("✅ Encontrados {$admins->count()} usuário(s) administrador(es):");
        $this->line('');

        $headers = ['ID', 'Nome', 'Email', 'Status', 'Criado em'];
        $rows = [];

        foreach ($admins as $admin) {
            $rows[] = [
                $admin->id,
                $admin->name,
                $admin->email,
                $admin->subscription_status,
                $admin->created_at->format('d/m/Y H:i:s'),
            ];
        }

        $this->table($headers, $rows);

        // Se usar spatie/laravel-permission, mostrar roles
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $this->line('');
            $this->info('🔐 Roles dos administradores:');
            
            foreach ($admins as $admin) {
                $roles = $admin->getRoleNames()->toArray();
                if (!empty($roles)) {
                    $this->line("   {$admin->name}: " . implode(', ', $roles));
                }
            }
        }

        return Command::SUCCESS;
    }
}