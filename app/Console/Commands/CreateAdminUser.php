<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--name=Admin} {--email=admin@qrcode.com} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar usuário administrador do sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Criando usuário administrador...');

        // Coletar dados do usuário
        $name = $this->option('name') ?: $this->ask('Nome do administrador', 'Admin');
        $email = $this->option('email') ?: $this->ask('Email do administrador', 'admin@qrcode.com');
        $password = $this->option('password') ?: $this->secret('Senha do administrador (mínimo 8 caracteres)');

        // Validar dados
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            $this->error('❌ Dados inválidos:');
            foreach ($validator->errors()->all() as $error) {
                $this->error("   - {$error}");
            }
            return Command::FAILURE;
        }

        // Verificar se já existe um admin
        if (User::where('email', $email)->exists()) {
            $this->error("❌ Já existe um usuário com o email: {$email}");
            return Command::FAILURE;
        }

        try {
            // Criar usuário admin
            $admin = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'subscription_status' => 'active', // Admin tem acesso total
                'trial_ends_at' => null, // Admin não tem trial
            ]);

            // Atribuir role de admin (se usar spatie/laravel-permission)
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
                $admin->assignRole($adminRole);
            }

            $this->info('✅ Usuário administrador criado com sucesso!');
            $this->line('');
            $this->line('📋 Detalhes do administrador:');
            $this->line("   Nome: {$admin->name}");
            $this->line("   Email: {$admin->email}");
            $this->line("   Status: {$admin->subscription_status}");
            $this->line("   Criado em: {$admin->created_at->format('d/m/Y H:i:s')}");
            $this->line('');
            $this->info('🔐 Você pode fazer login com essas credenciais.');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Erro ao criar usuário administrador: {$e->getMessage()}");
            return Command::FAILURE;
        }
    }
}