<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\QrCode;
use App\Models\Folder;
use App\Models\Team;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function complete_user_workflow_test()
    {
        // 1. Usuário se registra
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true
        ];

        $response = $this->post(route('register'), $userData);
        $response->assertRedirect();

        $user = User::where('email', 'joao@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('trialing', $user->subscription_status);
        $this->assertNotNull($user->trial_ends_at);

        // 2. Usuário faz login
        $response = $this->post(route('login'), [
            'email' => 'joao@example.com',
            'password' => 'password123'
        ]);
        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);

        // 3. Usuário acessa o dashboard
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);

        // 4. Usuário cria uma pasta
        $folderData = [
            'name' => 'Projetos',
            'description' => 'Pasta para projetos pessoais'
        ];

        $response = $this->post(route('folders.store'), $folderData);
        $response->assertRedirect();

        $folder = Folder::where('name', 'Projetos')->first();
        $this->assertNotNull($folder);

        // 5. Usuário cria um QR Code
        $qrCodeData = [
            'name' => 'Meu Site',
            'type' => 'url',
            'folder_id' => $folder->id,
            'content' => [
                'url' => 'https://meusite.com',
                'title' => 'Meu Site Pessoal'
            ],
            'design' => [
                'body_color' => '#000000',
                'background_color' => '#FFFFFF'
            ],
            'resolution' => 300,
            'format' => 'png'
        ];

        $response = $this->post(route('qrcodes.store'), $qrCodeData);
        $response->assertRedirect();

        $qrCode = QrCode::where('name', 'Meu Site')->first();
        $this->assertNotNull($qrCode);
        $this->assertEquals($folder->id, $qrCode->folder_id);

        // 6. Usuário visualiza o QR Code
        $response = $this->get(route('qrcodes.show', $qrCode));
        $response->assertStatus(200);

        // 7. Usuário baixa o QR Code
        $response = $this->get(route('qrcodes.download', $qrCode));
        $response->assertStatus(200);

        // 8. Usuário cria uma equipe
        $teamData = [
            'name' => 'Equipe de Desenvolvimento',
            'description' => 'Equipe responsável pelo desenvolvimento'
        ];

        $response = $this->post(route('teams.store'), $teamData);
        $response->assertRedirect();

        $team = Team::where('name', 'Equipe de Desenvolvimento')->first();
        $this->assertNotNull($team);

        // 9. Usuário compartilha QR Code com a equipe
        $qrCode->update(['team_id' => $team->id]);

        // 10. Usuário faz upgrade para premium
        Http::fake([
            'api.mercadopago.com/*' => Http::response([
                'id' => 'test-subscription-id',
                'status' => 'pending',
                'init_point' => 'https://www.mercadopago.com.br/checkout/v1/redirect?pref_id=test-pref-id'
            ], 200)
        ]);

        $response = $this->post(route('subscription.subscribe'), [
            'plan' => 'premium',
            'payment_method' => 'credit_card'
        ]);
        $response->assertRedirect();

        $subscription = Subscription::where('user_id', $user->id)->first();
        $this->assertNotNull($subscription);
        $this->assertEquals('premium', $subscription->plan_name);

        // 11. Simular webhook de pagamento aprovado
        $webhookData = [
            'type' => 'subscription',
            'action' => 'authorized',
            'data' => [
                'id' => $subscription->mp_subscription_id
            ]
        ];

        $response = $this->post(route('subscription.webhook'), $webhookData);
        $response->assertStatus(200);

        $user->refresh();
        $this->assertEquals('active', $user->subscription_status);

        // 12. Usuário cria QR Code dinâmico (funcionalidade premium)
        $dynamicQrData = [
            'name' => 'QR Dinâmico',
            'type' => 'url',
            'is_dynamic' => true,
            'content' => [
                'url' => 'https://exemplo.com'
            ],
            'design' => [
                'body_color' => '#FF0000',
                'background_color' => '#FFFFFF'
            ],
            'resolution' => 500,
            'format' => 'png'
        ];

        $response = $this->post(route('qrcodes.store'), $dynamicQrData);
        $response->assertRedirect();

        $dynamicQr = QrCode::where('name', 'QR Dinâmico')->first();
        $this->assertNotNull($dynamicQr);
        $this->assertTrue($dynamicQr->is_dynamic);

        // 13. Usuário acessa estatísticas (funcionalidade premium)
        $response = $this->get(route('qrcodes.show', $dynamicQr));
        $response->assertStatus(200);

        // 14. Usuário cria ticket de suporte
        $ticketData = [
            'subject' => 'Dúvida sobre funcionalidades',
            'message' => 'Gostaria de saber mais sobre as funcionalidades premium',
            'category' => 'general',
            'priority' => 'medium'
        ];

        $response = $this->post(route('support.store'), $ticketData);
        $response->assertRedirect();

        // 15. Usuário acessa página de ajuda
        $response = $this->get(route('help.index'));
        $response->assertStatus(200);

        $response = $this->get(route('help.faq'));
        $response->assertStatus(200);

        // 16. Usuário acessa configurações de privacidade
        $response = $this->get(route('privacy.index'));
        $response->assertStatus(200);

        // 17. Usuário faz logout
        $response = $this->post(route('logout'));
        $response->assertRedirect();
        $this->assertGuest();
    }

    /** @test */
    public function qr_code_scan_tracking_workflow()
    {
        $user = User::factory()->create();
        $qrCode = QrCode::factory()->create([
            'user_id' => $user->id,
            'short_code' => 'test123'
        ]);

        // Simular scan do QR Code
        $response = $this->get("/{$qrCode->short_code}");

        $response->assertRedirect();
        
        $qrCode->refresh();
        $this->assertEquals(1, $qrCode->scans_count);

        // Simular múltiplos scans
        for ($i = 0; $i < 5; $i++) {
            $this->get("/{$qrCode->short_code}");
        }

        $qrCode->refresh();
        $this->assertEquals(6, $qrCode->scans_count);
    }

    /** @test */
    public function team_collaboration_workflow()
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        // Owner cria equipe
        $this->actingAs($owner);
        
        $teamData = [
            'name' => 'Equipe Teste',
            'description' => 'Equipe para testes'
        ];

        $response = $this->post(route('teams.store'), $teamData);
        $response->assertRedirect();

        $team = Team::where('name', 'Equipe Teste')->first();

        // Owner convida membro
        $inviteData = [
            'email' => $member->email,
            'role' => 'editor'
        ];

        $response = $this->post(route('teams.addMember', $team), $inviteData);
        $response->assertRedirect();

        // Verificar se membro foi adicionado
        $this->assertTrue($team->members()->where('user_id', $member->id)->exists());

        // Owner cria QR Code e compartilha com equipe
        $qrCode = QrCode::factory()->create([
            'user_id' => $owner->id,
            'team_id' => $team->id
        ]);

        // Member faz login e acessa QR Code da equipe
        $this->actingAs($member);
        
        $response = $this->get(route('qrcodes.show', $qrCode));
        $response->assertStatus(200);
    }

    /** @test */
    public function subscription_lifecycle_workflow()
    {
        $user = User::factory()->create([
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->addDays(7)
        ]);

        $this->actingAs($user);

        // Usuário em trial pode acessar funcionalidades básicas
        $response = $this->get(route('qrcodes.index'));
        $response->assertStatus(200);

        // Usuário faz upgrade
        Http::fake([
            'api.mercadopago.com/*' => Http::response([
                'id' => 'test-subscription-id',
                'status' => 'pending'
            ], 200)
        ]);

        $response = $this->post(route('subscription.subscribe'), [
            'plan' => 'premium',
            'payment_method' => 'credit_card'
        ]);
        $response->assertRedirect();

        // Simular pagamento aprovado
        $subscription = Subscription::where('user_id', $user->id)->first();
        
        $webhookData = [
            'type' => 'subscription',
            'action' => 'authorized',
            'data' => ['id' => $subscription->mp_subscription_id]
        ];

        $this->post(route('subscription.webhook'), $webhookData);

        $user->refresh();
        $this->assertEquals('active', $user->subscription_status);

        // Usuário pode acessar funcionalidades premium
        $response = $this->get(route('domains.index'));
        $response->assertStatus(200);

        // Usuário cancela assinatura
        Http::fake([
            'api.mercadopago.com/*' => Http::response([
                'status' => 'cancelled'
            ], 200)
        ]);

        $response = $this->post(route('subscription.cancel'));
        $response->assertRedirect();

        $subscription->refresh();
        $this->assertEquals('cancelled', $subscription->status);
    }
}
