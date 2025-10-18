<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:reset-password {email} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resetar senha de um usuário administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->option('password') ?: $this->secret('Nova senha (mínimo 8 caracteres)');

        $this->info("🔧 Resetando senha do administrador: {$email}");

        // Validar senha
        $validator = Validator::make([
            'password' => $password,
        ], [
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Senha inválida:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("   - {$error}");
            }
            return Command::FAILURE;
        }

        // Buscar usuário
        $admin = User::where('email', $email)
            ->where('subscription_status', 'active')
            ->whereNull('trial_ends_at')
            ->first();

        if (!$admin) {
            $this->error("❌ Usuário administrador não encontrado: {$email}");
            $this->line('');
            $this->info('💡 Para listar administradores, execute:');
            $this->line('   php artisan admin:list');
            return Command::FAILURE;
        }

        try {
            // Atualizar senha
            $admin->update([
                'password' => Hash::make($password),
            ]);

            $this->info('✅ Senha resetada com sucesso!');
            $this->line('');
            $this->line('📋 Detalhes do administrador:');
            $this->line("   Nome: {$admin->name}");
            $this->line("   Email: {$admin->email}");
            $this->line("   Nova senha: {$password}");
            $this->line("   Atualizado em: " . now()->format('d/m/Y H:i:s'));
            $this->line('');
            $this->info('🔐 O administrador pode fazer login com a nova senha.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Erro ao resetar senha: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}