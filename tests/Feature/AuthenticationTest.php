<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user->trial_ends_at);
        $this->assertEquals('trialing', $user->subscription_status);
    }

    /** @test */
    public function user_registration_requires_valid_email()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function user_registration_requires_password_confirmation()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
            'terms' => true
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors(['password']);
    }

    /** @test */
    public function user_registration_requires_terms_acceptance()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => false
        ];

        $response = $this->post(route('register'), $userData);

        $response->assertSessionHasErrors(['terms']);
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post(route('login'), [
            'email' => 'john@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post(route('login'), [
            'email' => 'john@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect();
        $this->assertGuest();
    }

    /** @test */
    public function unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
    }

    /** @test */
    public function user_can_request_password_reset()
    {
        $user = User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post(route('password.email'), [
            'email' => 'john@example.com'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }

    /** @test */
    public function user_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create(['email' => 'john@example.com']);
        $token = app('auth.password.broker')->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function user_cannot_reset_password_with_invalid_token()
    {
        $user = User::factory()->create(['email' => 'john@example.com']);

        $response = $this->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => 'john@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function user_registration_sets_trial_period()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true
        ];

        $this->post(route('register'), $userData);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertNotNull($user->trial_ends_at);
        $this->assertTrue($user->trial_ends_at->isFuture());
        $this->assertEquals('trialing', $user->subscription_status);
    }

    /** @test */
    public function user_can_verify_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $this->assertFalse($user->hasVerifiedEmail());

        $user->markEmailAsVerified();

        $this->assertTrue($user->hasVerifiedEmail());
    }

    /** @test */
    public function unverified_user_cannot_access_protected_routes()
    {
        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('verification.notice'));
    }

    /** @test */
    public function verified_user_can_access_protected_routes()
    {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_change_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'oldpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertRedirect();
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }

    /** @test */
    public function user_cannot_change_password_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword')
        ]);

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'wrongpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123'
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    /** @test */
    public function login_is_rate_limited()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        // Attempt login 6 times (more than the throttle limit)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('login'), [
                'email' => 'john@example.com',
                'password' => 'wrong-password'
            ]);
        }

        $response->assertSessionHasErrors(['email']);
    }
}
