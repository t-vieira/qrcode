<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'subscription_status' => 'trialing',
            'trial_ends_at' => now()->addDays(7)
        ]);
    }

    /** @test */
    public function authenticated_user_can_view_subscription_upgrade_page()
    {
        $response = $this->actingAs($this->user)->get(route('subscription.upgrade'));

        $response->assertStatus(200);
        $response->assertViewIs('subscription.upgrade');
    }

    /** @test */
    public function user_can_subscribe_to_premium_plan()
    {
        // Mock Mercado Pago API response
        Http::fake([
            'api.mercadopago.com/*' => Http::response([
                'id' => 'test-subscription-id',
                'status' => 'pending',
                'init_point' => 'https://www.mercadopago.com.br/checkout/v1/redirect?pref_id=test-pref-id'
            ], 200)
        ]);

        $response = $this->actingAs($this->user)->post(route('subscription.subscribe'), [
            'plan' => 'premium',
            'payment_method' => 'credit_card'
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->user->id,
            'plan_name' => 'premium',
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function user_can_create_pix_payment()
    {
        // Mock Mercado Pago API response
        Http::fake([
            'api.mercadopago.com/*' => Http::response([
                'id' => 'test-payment-id',
                'status' => 'pending',
                'point_of_interaction' => [
                    'transaction_data' => [
                        'qr_code' => 'test-qr-code',
                        'qr_code_base64' => 'data:image/png;base64,test-image'
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($this->user)->post(route('subscription.pix'), [
            'plan' => 'premium'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'payment_id',
            'qr_code',
            'qr_code_base64'
        ]);
    }

    /** @test */
    public function user_can_cancel_subscription()
    {
        $subscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'authorized'
        ]);

        // Mock Mercado Pago API response
        Http::fake([
            'api.mercadopago.com/*' => Http::response([
                'status' => 'cancelled'
            ], 200)
        ]);

        $response = $this->actingAs($this->user)->post(route('subscription.cancel'));

        $response->assertRedirect();
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled'
        ]);
    }

    /** @test */
    public function webhook_can_process_payment_authorized()
    {
        $subscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'mp_subscription_id' => 'test-subscription-id',
            'status' => 'pending'
        ]);

        $webhookData = [
            'type' => 'subscription',
            'action' => 'authorized',
            'data' => [
                'id' => 'test-subscription-id'
            ]
        ];

        $response = $this->post(route('subscription.webhook'), $webhookData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'authorized'
        ]);
    }

    /** @test */
    public function webhook_can_process_payment_cancelled()
    {
        $subscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'mp_subscription_id' => 'test-subscription-id',
            'status' => 'authorized'
        ]);

        $webhookData = [
            'type' => 'subscription',
            'action' => 'cancelled',
            'data' => [
                'id' => 'test-subscription-id'
            ]
        ];

        $response = $this->post(route('subscription.webhook'), $webhookData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled'
        ]);
    }

    /** @test */
    public function user_can_view_subscription_status()
    {
        $subscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'authorized'
        ]);

        $response = $this->actingAs($this->user)->get(route('subscription.status'));

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'authorized',
            'plan_name' => $subscription->plan_name
        ]);
    }

    /** @test */
    public function user_can_view_payment_success_page()
    {
        $response = $this->actingAs($this->user)->get(route('subscription.success'));

        $response->assertStatus(200);
        $response->assertViewIs('subscription.result');
    }

    /** @test */
    public function user_can_view_payment_failure_page()
    {
        $response = $this->actingAs($this->user)->get(route('subscription.failure'));

        $response->assertStatus(200);
        $response->assertViewIs('subscription.result');
    }

    /** @test */
    public function user_can_view_payment_pending_page()
    {
        $response = $this->actingAs($this->user)->get(route('subscription.pending'));

        $response->assertStatus(200);
        $response->assertViewIs('subscription.result');
    }

    /** @test */
    public function trial_user_has_access_to_basic_features()
    {
        $this->assertTrue($this->user->isOnTrial());
        $this->assertTrue($this->user->canAccessAdvancedFeatures());
    }

    /** @test */
    public function expired_trial_user_has_limited_access()
    {
        $this->user->update([
            'trial_ends_at' => now()->subDays(1),
            'subscription_status' => 'expired'
        ]);

        $this->assertFalse($this->user->isOnTrial());
        $this->assertFalse($this->user->canAccessAdvancedFeatures());
    }

    /** @test */
    public function active_subscription_user_has_full_access()
    {
        $this->user->update([
            'subscription_status' => 'active'
        ]);

        Subscription::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'authorized'
        ]);

        $this->assertTrue($this->user->hasActiveSubscription());
        $this->assertTrue($this->user->canAccessAdvancedFeatures());
    }

    /** @test */
    public function subscription_creation_requires_valid_plan()
    {
        $response = $this->actingAs($this->user)->post(route('subscription.subscribe'), [
            'plan' => 'invalid-plan',
            'payment_method' => 'credit_card'
        ]);

        $response->assertSessionHasErrors(['plan']);
    }

    /** @test */
    public function subscription_creation_requires_valid_payment_method()
    {
        $response = $this->actingAs($this->user)->post(route('subscription.subscribe'), [
            'plan' => 'premium',
            'payment_method' => 'invalid-method'
        ]);

        $response->assertSessionHasErrors(['payment_method']);
    }

    /** @test */
    public function webhook_requires_valid_signature()
    {
        $webhookData = [
            'type' => 'subscription',
            'action' => 'authorized',
            'data' => [
                'id' => 'test-subscription-id'
            ]
        ];

        $response = $this->post(route('subscription.webhook'), $webhookData, [
            'HTTP_X_SIGNATURE' => 'invalid-signature'
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function user_cannot_subscribe_twice()
    {
        Subscription::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'authorized'
        ]);

        $response = $this->actingAs($this->user)->post(route('subscription.subscribe'), [
            'plan' => 'premium',
            'payment_method' => 'credit_card'
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function subscription_webhook_updates_user_status()
    {
        $subscription = Subscription::factory()->create([
            'user_id' => $this->user->id,
            'mp_subscription_id' => 'test-subscription-id',
            'status' => 'pending'
        ]);

        $webhookData = [
            'type' => 'subscription',
            'action' => 'authorized',
            'data' => [
                'id' => 'test-subscription-id'
            ]
        ];

        $this->post(route('subscription.webhook'), $webhookData);

        $this->user->refresh();
        $this->assertEquals('active', $this->user->subscription_status);
    }
}
