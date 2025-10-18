<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\QrCode;
use App\Models\Folder;
use App\Models\QrScan;

class QrCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Criar pastas para o usuário
            $folders = [
                Folder::create([
                    'user_id' => $user->id,
                    'name' => 'Pessoal',
                    'slug' => 'pessoal',
                ]),
                Folder::create([
                    'user_id' => $user->id,
                    'name' => 'Trabalho',
                    'slug' => 'trabalho',
                ]),
                Folder::create([
                    'user_id' => $user->id,
                    'name' => 'Marketing',
                    'slug' => 'marketing',
                ]),
            ];

            // QR Codes de exemplo para cada usuário
            $qrCodes = [
                // QR Code de URL
                QrCode::create([
                    'user_id' => $user->id,
                    'folder_id' => $folders[0]->id,
                    'name' => 'Meu Site Pessoal',
                    'type' => 'url',
                    'short_code' => 'site-' . $user->id,
                    'is_dynamic' => false,
                    'content' => [
                        'url' => 'https://meusite.com',
                        'title' => 'Meu Site Pessoal',
                        'description' => 'Visite meu site pessoal'
                    ],
                    'design' => [
                        'body_color' => '#000000',
                        'background_color' => '#FFFFFF',
                        'eye_color' => '#000000',
                        'shape' => 'square'
                    ],
                    'resolution' => 300,
                    'format' => 'png',
                    'status' => 'active',
                    'scans_count' => rand(10, 100),
                ]),

                // QR Code de vCard
                QrCode::create([
                    'user_id' => $user->id,
                    'folder_id' => $folders[1]->id,
                    'name' => 'Cartão de Visita',
                    'type' => 'vcard',
                    'short_code' => 'vcard-' . $user->id,
                    'is_dynamic' => true,
                    'content' => [
                        'first_name' => $user->name,
                        'last_name' => 'Silva',
                        'organization' => 'Empresa Exemplo',
                        'title' => 'Desenvolvedor',
                        'phone' => '+55 11 99999-9999',
                        'email' => $user->email,
                        'website' => 'https://empresa.com',
                        'address' => 'Rua Exemplo, 123',
                        'city' => 'São Paulo',
                        'state' => 'SP',
                        'zip' => '01234-567',
                        'country' => 'Brasil'
                    ],
                    'design' => [
                        'body_color' => '#1E40AF',
                        'background_color' => '#FFFFFF',
                        'eye_color' => '#1E40AF',
                        'shape' => 'rounded'
                    ],
                    'resolution' => 500,
                    'format' => 'png',
                    'status' => 'active',
                    'scans_count' => rand(5, 50),
                ]),

                // QR Code de Wi-Fi
                QrCode::create([
                    'user_id' => $user->id,
                    'folder_id' => $folders[2]->id,
                    'name' => 'Wi-Fi do Escritório',
                    'type' => 'wifi',
                    'short_code' => 'wifi-' . $user->id,
                    'is_dynamic' => false,
                    'content' => [
                        'ssid' => 'Escritorio-Exemplo',
                        'password' => 'senha123',
                        'security' => 'WPA',
                        'hidden' => false
                    ],
                    'design' => [
                        'body_color' => '#059669',
                        'background_color' => '#FFFFFF',
                        'eye_color' => '#059669',
                        'shape' => 'square'
                    ],
                    'resolution' => 400,
                    'format' => 'png',
                    'status' => 'active',
                    'scans_count' => rand(20, 80),
                ]),

                // QR Code de Texto
                QrCode::create([
                    'user_id' => $user->id,
                    'folder_id' => $folders[0]->id,
                    'name' => 'Mensagem de Boas-vindas',
                    'type' => 'text',
                    'short_code' => 'text-' . $user->id,
                    'is_dynamic' => true,
                    'content' => [
                        'text' => 'Bem-vindo ao nosso estabelecimento! Obrigado pela sua visita.'
                    ],
                    'design' => [
                        'body_color' => '#7C3AED',
                        'background_color' => '#FFFFFF',
                        'eye_color' => '#7C3AED',
                        'shape' => 'rounded'
                    ],
                    'resolution' => 300,
                    'format' => 'png',
                    'status' => 'active',
                    'scans_count' => rand(15, 60),
                ]),

                // QR Code de E-mail
                QrCode::create([
                    'user_id' => $user->id,
                    'folder_id' => $folders[1]->id,
                    'name' => 'Contato por E-mail',
                    'type' => 'email',
                    'short_code' => 'email-' . $user->id,
                    'is_dynamic' => false,
                    'content' => [
                        'email' => $user->email,
                        'subject' => 'Contato via QR Code',
                        'body' => 'Olá! Entrei em contato através do QR Code.'
                    ],
                    'design' => [
                        'body_color' => '#DC2626',
                        'background_color' => '#FFFFFF',
                        'eye_color' => '#DC2626',
                        'shape' => 'square'
                    ],
                    'resolution' => 350,
                    'format' => 'png',
                    'status' => 'active',
                    'scans_count' => rand(8, 40),
                ]),

                // QR Code de Telefone
                QrCode::create([
                    'user_id' => $user->id,
                    'folder_id' => $folders[2]->id,
                    'name' => 'Ligar Agora',
                    'type' => 'phone',
                    'short_code' => 'phone-' . $user->id,
                    'is_dynamic' => false,
                    'content' => [
                        'phone' => '+55 11 99999-9999'
                    ],
                    'design' => [
                        'body_color' => '#EA580C',
                        'background_color' => '#FFFFFF',
                        'eye_color' => '#EA580C',
                        'shape' => 'rounded'
                    ],
                    'resolution' => 300,
                    'format' => 'png',
                    'status' => 'active',
                    'scans_count' => rand(12, 45),
                ]),

                // QR Code de SMS
                QrCode::create([
                    'user_id' => $user->id,
                    'folder_id' => $folders[0]->id,
                    'name' => 'Enviar SMS',
                    'type' => 'sms',
                    'short_code' => 'sms-' . $user->id,
                    'is_dynamic' => true,
                    'content' => [
                        'phone' => '+55 11 99999-9999',
                        'message' => 'Olá! Gostaria de mais informações sobre seus serviços.'
                    ],
                    'design' => [
                        'body_color' => '#0891B2',
                        'background_color' => '#FFFFFF',
                        'eye_color' => '#0891B2',
                        'shape' => 'square'
                    ],
                    'resolution' => 300,
                    'format' => 'png',
                    'status' => 'active',
                    'scans_count' => rand(6, 30),
                ]),

                // QR Code de Localização
                QrCode::create([
                    'user_id' => $user->id,
                    'folder_id' => $folders[2]->id,
                    'name' => 'Localização do Escritório',
                    'type' => 'location',
                    'short_code' => 'location-' . $user->id,
                    'is_dynamic' => false,
                    'content' => [
                        'name' => 'Escritório Principal',
                        'address' => 'Av. Paulista, 1000 - Bela Vista, São Paulo - SP',
                        'latitude' => -23.5613,
                        'longitude' => -46.6565
                    ],
                    'design' => [
                        'body_color' => '#16A34A',
                        'background_color' => '#FFFFFF',
                        'eye_color' => '#16A34A',
                        'shape' => 'rounded'
                    ],
                    'resolution' => 400,
                    'format' => 'png',
                    'status' => 'active',
                    'scans_count' => rand(25, 90),
                ]),
            ];

            // Criar alguns scans para cada QR Code
            foreach ($qrCodes as $qrCode) {
                $this->createScansForQrCode($qrCode);
            }
        }
    }

    /**
     * Criar scans de exemplo para um QR Code
     */
    private function createScansForQrCode(QrCode $qrCode): void
    {
        $scanCount = $qrCode->scans_count;
        $countries = ['Brasil', 'Argentina', 'Chile', 'Colômbia', 'México', 'Estados Unidos'];
        $cities = ['São Paulo', 'Rio de Janeiro', 'Buenos Aires', 'Santiago', 'Bogotá', 'Cidade do México', 'Nova York'];
        $devices = ['mobile', 'desktop', 'tablet'];
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge'];
        $operatingSystems = ['Windows', 'macOS', 'Linux', 'iOS', 'Android'];

        for ($i = 0; $i < $scanCount; $i++) {
            $isUnique = $i < ($scanCount * 0.7); // 70% dos scans são únicos

            QrScan::create([
                'qr_code_id' => $qrCode->id,
                'ip_address' => '192.168.1.' . rand(1, 254),
                'country' => $countries[array_rand($countries)],
                'city' => $cities[array_rand($cities)],
                'latitude' => -23.5 + (rand(-100, 100) / 100),
                'longitude' => -46.6 + (rand(-100, 100) / 100),
                'device_type' => $devices[array_rand($devices)],
                'browser' => $browsers[array_rand($browsers)],
                'os' => $operatingSystems[array_rand($operatingSystems)],
                'is_unique' => $isUnique,
                'scanned_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59)),
            ]);
        }
    }
}
