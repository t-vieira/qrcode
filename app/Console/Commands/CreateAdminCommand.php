<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create {--email=admin@qrcode.com} {--password=admin123456} {--name=Administrador}';
    protected $description = 'Criar usuário administrador do sistema';

    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Verificar se já existe
        if (User::where('email', $email)->exists()) {
            $this->error("❌ Usuário com email {$email} já existe!");
            return 1;
        }

        // Criar usuário admin
        $admin = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'subscription_status' => 'active',
            'trial_ends_at' => null,
        ]);

        // Criar role admin se não existir
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole($adminRole);

        $this->info("✅ Usuário administrador criado com sucesso!");
        $this->line("📧 Email: {$email}");
        $this->line("🔑 Senha: {$password}");
        $this->line("👤 Nome: {$name}");

        return 0;
    }
}
