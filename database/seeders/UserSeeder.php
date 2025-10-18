<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuário administrador
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@qrcodesaas.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'trial_ends_at' => now()->addDays(7),
            'subscription_status' => 'active',
        ]);

        // Usuário com trial ativo
        $trialUser = User::create([
            'name' => 'Usuário Trial',
            'email' => 'trial@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'trial_ends_at' => now()->addDays(5),
            'subscription_status' => 'trialing',
        ]);

        // Usuário com assinatura ativa
        $premiumUser = User::create([
            'name' => 'Usuário Premium',
            'email' => 'premium@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'trial_ends_at' => now()->subDays(1),
            'subscription_status' => 'active',
        ]);

        // Criar assinatura para o usuário premium
        Subscription::create([
            'user_id' => $premiumUser->id,
            'mp_subscription_id' => 'MP_SUBSCRIPTION_' . uniqid(),
            'status' => 'authorized',
            'plan_name' => 'premium',
            'amount' => 29.90,
            'current_period_start' => now()->startOfMonth(),
            'current_period_end' => now()->endOfMonth(),
        ]);

        // Usuário com trial expirado
        $expiredUser = User::create([
            'name' => 'Usuário Expirado',
            'email' => 'expired@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'trial_ends_at' => now()->subDays(3),
            'subscription_status' => 'expired',
        ]);

        // Usuário com assinatura cancelada
        $cancelledUser = User::create([
            'name' => 'Usuário Cancelado',
            'email' => 'cancelled@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'trial_ends_at' => now()->subDays(10),
            'subscription_status' => 'canceled',
        ]);

        // Criar assinatura cancelada
        Subscription::create([
            'user_id' => $cancelledUser->id,
            'mp_subscription_id' => 'MP_SUBSCRIPTION_' . uniqid(),
            'status' => 'cancelled',
            'plan_name' => 'premium',
            'amount' => 29.90,
            'current_period_start' => now()->subMonth()->startOfMonth(),
            'current_period_end' => now()->subMonth()->endOfMonth(),
            'canceled_at' => now()->subDays(5),
        ]);

        // Usuários adicionais para testes
        User::factory(10)->create([
            'trial_ends_at' => now()->addDays(rand(1, 7)),
            'subscription_status' => 'trialing',
        ]);

        User::factory(5)->create([
            'trial_ends_at' => now()->subDays(rand(1, 10)),
            'subscription_status' => 'expired',
        ]);

        User::factory(3)->create([
            'trial_ends_at' => now()->subDays(rand(1, 5)),
            'subscription_status' => 'active',
        ]);
    }
}
