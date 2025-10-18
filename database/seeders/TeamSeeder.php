<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use App\Models\QrCode;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::take(5)->get();

        foreach ($users as $user) {
            // Criar equipe para o usuário
            $team = Team::create([
                'owner_id' => $user->id,
                'name' => 'Equipe ' . $user->name,
                'slug' => 'equipe-' . strtolower(str_replace(' ', '-', $user->name)),
                'description' => 'Equipe de trabalho de ' . $user->name,
                'status' => 'active',
            ]);

            // Adicionar alguns membros à equipe
            $otherUsers = User::where('id', '!=', $user->id)->take(3)->get();
            
            foreach ($otherUsers as $member) {
                $team->members()->attach($member->id, [
                    'role' => $this->getRandomRole(),
                    'permissions' => $this->getRandomPermissions(),
                ]);
            }

            // Compartilhar alguns QR Codes com a equipe
            $userQrCodes = QrCode::where('user_id', $user->id)->take(3)->get();
            
            foreach ($userQrCodes as $qrCode) {
                $qrCode->update(['team_id' => $team->id]);
            }
        }

        // Criar algumas equipes adicionais
        $additionalUsers = User::skip(5)->take(3)->get();
        
        foreach ($additionalUsers as $user) {
            $team = Team::create([
                'owner_id' => $user->id,
                'name' => 'Projeto ' . uniqid(),
                'slug' => 'projeto-' . uniqid(),
                'description' => 'Equipe para projeto específico',
                'status' => 'active',
            ]);

            // Adicionar membros
            $members = User::where('id', '!=', $user->id)->take(2)->get();
            
            foreach ($members as $member) {
                $team->members()->attach($member->id, [
                    'role' => 'editor',
                    'permissions' => [
                        'view_qr_codes' => true,
                        'create_qr_codes' => true,
                        'edit_qr_codes' => true,
                        'view_statistics' => true,
                    ],
                ]);
            }
        }
    }

    /**
     * Obter função aleatória
     */
    private function getRandomRole(): string
    {
        $roles = ['admin', 'editor', 'viewer'];
        return $roles[array_rand($roles)];
    }

    /**
     * Obter permissões aleatórias baseadas na função
     */
    private function getRandomPermissions(): array
    {
        $permissions = [
            'view_qr_codes' => true,
            'create_qr_codes' => rand(0, 1) == 1,
            'edit_qr_codes' => rand(0, 1) == 1,
            'delete_qr_codes' => rand(0, 1) == 1,
            'view_statistics' => rand(0, 1) == 1,
            'export_reports' => rand(0, 1) == 1,
            'manage_folders' => rand(0, 1) == 1,
            'manage_team' => rand(0, 1) == 1,
            'invite_members' => rand(0, 1) == 1,
            'remove_members' => rand(0, 1) == 1,
        ];

        return $permissions;
    }
}
