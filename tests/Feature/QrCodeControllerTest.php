<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\QrCode;
use App\Models\Folder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class QrCodeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Storage::fake('public');
    }

    /** @test */
    public function authenticated_user_can_view_qr_codes_index()
    {
        $response = $this->actingAs($this->user)->get(route('qrcodes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('qrcodes.index');
    }

    /** @test */
    public function unauthenticated_user_cannot_view_qr_codes_index()
    {
        $response = $this->get(route('qrcodes.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_view_create_qr_code_form()
    {
        $response = $this->actingAs($this->user)->get(route('qrcodes.create'));

        $response->assertStatus(200);
        $response->assertViewIs('qrcodes.create');
    }

    /** @test */
    public function authenticated_user_can_create_url_qr_code()
    {
        $data = [
            'name' => 'Test QR Code',
            'type' => 'url',
            'content' => [
                'url' => 'https://example.com',
                'title' => 'Example Website'
            ],
            'design' => [
                'body_color' => '#000000',
                'background_color' => '#FFFFFF'
            ],
            'resolution' => 300,
            'format' => 'png'
        ];

        $response = $this->actingAs($this->user)->post(route('qrcodes.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('qr_codes', [
            'name' => 'Test QR Code',
            'type' => 'url',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function authenticated_user_can_create_vcard_qr_code()
    {
        $data = [
            'name' => 'My Business Card',
            'type' => 'vcard',
            'content' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'organization' => 'Example Corp',
                'phone' => '+1234567890',
                'email' => 'john@example.com'
            ],
            'design' => [
                'body_color' => '#000000',
                'background_color' => '#FFFFFF'
            ],
            'resolution' => 300,
            'format' => 'png'
        ];

        $response = $this->actingAs($this->user)->post(route('qrcodes.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('qr_codes', [
            'name' => 'My Business Card',
            'type' => 'vcard',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function authenticated_user_can_create_dynamic_qr_code()
    {
        $data = [
            'name' => 'Dynamic QR Code',
            'type' => 'url',
            'is_dynamic' => true,
            'content' => [
                'url' => 'https://example.com'
            ],
            'design' => [
                'body_color' => '#000000',
                'background_color' => '#FFFFFF'
            ],
            'resolution' => 300,
            'format' => 'png'
        ];

        $response = $this->actingAs($this->user)->post(route('qrcodes.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('qr_codes', [
            'name' => 'Dynamic QR Code',
            'is_dynamic' => true,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function qr_code_creation_requires_valid_data()
    {
        $data = [
            'name' => '', // Invalid: empty name
            'type' => 'invalid_type', // Invalid: unsupported type
            'content' => [] // Invalid: empty content
        ];

        $response = $this->actingAs($this->user)->post(route('qrcodes.store'), $data);

        $response->assertSessionHasErrors(['name', 'type', 'content']);
    }

    /** @test */
    public function authenticated_user_can_view_their_qr_code()
    {
        $qrCode = QrCode::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('qrcodes.show', $qrCode));

        $response->assertStatus(200);
        $response->assertViewIs('qrcodes.show');
        $response->assertViewHas('qrCode', $qrCode);
    }

    /** @test */
    public function user_cannot_view_other_users_qr_code()
    {
        $otherUser = User::factory()->create();
        $qrCode = QrCode::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get(route('qrcodes.show', $qrCode));

        $response->assertStatus(403);
    }

    /** @test */
    public function authenticated_user_can_edit_their_qr_code()
    {
        $qrCode = QrCode::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('qrcodes.edit', $qrCode));

        $response->assertStatus(200);
        $response->assertViewIs('qrcodes.edit');
        $response->assertViewHas('qrCode', $qrCode);
    }

    /** @test */
    public function authenticated_user_can_update_their_qr_code()
    {
        $qrCode = QrCode::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'name' => 'Updated QR Code',
            'type' => 'url',
            'content' => [
                'url' => 'https://updated-example.com'
            ],
            'design' => [
                'body_color' => '#FF0000',
                'background_color' => '#FFFFFF'
            ],
            'resolution' => 500,
            'format' => 'png'
        ];

        $response = $this->actingAs($this->user)->put(route('qrcodes.update', $qrCode), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('qr_codes', [
            'id' => $qrCode->id,
            'name' => 'Updated QR Code'
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_their_qr_code()
    {
        $qrCode = QrCode::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete(route('qrcodes.destroy', $qrCode));

        $response->assertRedirect();
        $this->assertSoftDeleted('qr_codes', ['id' => $qrCode->id]);
    }

    /** @test */
    public function authenticated_user_can_download_their_qr_code()
    {
        $qrCode = QrCode::factory()->create([
            'user_id' => $this->user->id,
            'format' => 'png'
        ]);

        $response = $this->actingAs($this->user)->get(route('qrcodes.download', $qrCode));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /** @test */
    public function authenticated_user_can_preview_their_qr_code()
    {
        $qrCode = QrCode::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('qrcodes.preview', $qrCode));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    /** @test */
    public function authenticated_user_can_create_qr_code_in_folder()
    {
        $folder = Folder::factory()->create(['user_id' => $this->user->id]);

        $data = [
            'name' => 'QR Code in Folder',
            'type' => 'url',
            'folder_id' => $folder->id,
            'content' => [
                'url' => 'https://example.com'
            ],
            'design' => [
                'body_color' => '#000000',
                'background_color' => '#FFFFFF'
            ],
            'resolution' => 300,
            'format' => 'png'
        ];

        $response = $this->actingAs($this->user)->post(route('qrcodes.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('qr_codes', [
            'name' => 'QR Code in Folder',
            'folder_id' => $folder->id,
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function authenticated_user_can_create_qr_code_with_custom_short_code()
    {
        $data = [
            'name' => 'Custom Short Code QR',
            'type' => 'url',
            'short_code' => 'custom123',
            'content' => [
                'url' => 'https://example.com'
            ],
            'design' => [
                'body_color' => '#000000',
                'background_color' => '#FFFFFF'
            ],
            'resolution' => 300,
            'format' => 'png'
        ];

        $response = $this->actingAs($this->user)->post(route('qrcodes.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('qr_codes', [
            'name' => 'Custom Short Code QR',
            'short_code' => 'custom123',
            'user_id' => $this->user->id
        ]);
    }

    /** @test */
    public function qr_code_creation_fails_with_duplicate_short_code()
    {
        // Create first QR code with short code
        QrCode::factory()->create([
            'user_id' => $this->user->id,
            'short_code' => 'duplicate123'
        ]);

        $data = [
            'name' => 'Duplicate Short Code QR',
            'type' => 'url',
            'short_code' => 'duplicate123', // Same short code
            'content' => [
                'url' => 'https://example.com'
            ],
            'design' => [
                'body_color' => '#000000',
                'background_color' => '#FFFFFF'
            ],
            'resolution' => 300,
            'format' => 'png'
        ];

        $response = $this->actingAs($this->user)->post(route('qrcodes.store'), $data);

        $response->assertSessionHasErrors(['short_code']);
    }

    /** @test */
    public function authenticated_user_can_upload_logo_for_qr_code()
    {
        $logo = UploadedFile::fake()->image('logo.png', 100, 100);

        $data = [
            'name' => 'QR Code with Logo',
            'type' => 'url',
            'content' => [
                'url' => 'https://example.com'
            ],
            'design' => [
                'body_color' => '#000000',
                'background_color' => '#FFFFFF',
                'logo' => $logo
            ],
            'resolution' => 300,
            'format' => 'png'
        ];

        $response = $this->actingAs($this->user)->post(route('qrcodes.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('qr_codes', [
            'name' => 'QR Code with Logo',
            'user_id' => $this->user->id
        ]);
    }
}
