<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se já existe um admin
        if (User::where('email', 'admin@qrcode.com')->exists()) {
            $this->command->info('👤 Usuário admin já existe.');
            return;
        }

        // Criar usuário administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@qrcode.com',
            'password' => Hash::make('admin123456'),
            'email_verified_at' => now(),
            'subscription_status' => 'active',
            'trial_ends_at' => null,
        ]);

        // Atribuir role de admin (se usar spatie/laravel-permission)
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
            $admin->assignRole($adminRole);
        }

        $this->command->info('✅ Usuário administrador criado:');
        $this->command->line("   Email: admin@qrcode.com");
        $this->command->line("   Senha: admin123456");
        $this->command->line("   Status: active");
    }
}