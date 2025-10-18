<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ShortUrlService;
use App\Models\QrCode;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShortUrlServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ShortUrlService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ShortUrlService();
    }

    /** @test */
    public function it_can_generate_short_code()
    {
        $shortCode = $this->service->generateShortCode();

        $this->assertIsString($shortCode);
        $this->assertGreaterThanOrEqual(6, strlen($shortCode));
        $this->assertLessThanOrEqual(20, strlen($shortCode));
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9\-_]+$/', $shortCode);
    }

    /** @test */
    public function it_generates_unique_short_codes()
    {
        $codes = [];
        
        for ($i = 0; $i < 100; $i++) {
            $code = $this->service->generateShortCode();
            $this->assertNotContains($code, $codes);
            $codes[] = $code;
        }
    }

    /** @test */
    public function it_can_validate_custom_code()
    {
        $validCodes = [
            'abc123',
            'my-qr-code',
            'test_code',
            '123456',
            'a-b-c-1-2-3'
        ];

        foreach ($validCodes as $code) {
            $this->assertTrue($this->service->validateCustomCode($code));
        }
    }

    /** @test */
    public function it_rejects_invalid_custom_codes()
    {
        $invalidCodes = [
            'abc@123',  // Contains @
            'abc 123',  // Contains space
            'abc.123',  // Contains dot
            'abc/123',  // Contains slash
            'abc+123',  // Contains plus
            'abc=123',  // Contains equals
            'abc?123',  // Contains question mark
            'abc#123',  // Contains hash
            'abc%123',  // Contains percent
            'abc&123',  // Contains ampersand
            '',         // Empty
            'a',        // Too short
            str_repeat('a', 21), // Too long
        ];

        foreach ($invalidCodes as $code) {
            $this->assertFalse($this->service->validateCustomCode($code));
        }
    }

    /** @test */
    public function it_checks_code_availability()
    {
        // Create a QR code with a specific short code
        QrCode::factory()->create(['short_code' => 'test123']);

        $this->assertFalse($this->service->isCodeAvailable('test123'));
        $this->assertTrue($this->service->isCodeAvailable('available123'));
    }

    /** @test */
    public function it_can_generate_short_code_with_custom_length()
    {
        $shortCode = $this->service->generateShortCode(8);

        $this->assertIsString($shortCode);
        $this->assertEquals(8, strlen($shortCode));
    }

    /** @test */
    public function it_can_generate_short_code_with_custom_charset()
    {
        $shortCode = $this->service->generateShortCode(6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');

        $this->assertIsString($shortCode);
        $this->assertEquals(6, strlen($shortCode));
        $this->assertMatchesRegularExpression('/^[A-Z]+$/', $shortCode);
    }

    /** @test */
    public function it_handles_case_sensitivity()
    {
        QrCode::factory()->create(['short_code' => 'Test123']);

        $this->assertFalse($this->service->isCodeAvailable('test123'));
        $this->assertFalse($this->service->isCodeAvailable('TEST123'));
        $this->assertTrue($this->service->isCodeAvailable('test124'));
    }

    /** @test */
    public function it_can_suggest_alternative_codes()
    {
        QrCode::factory()->create(['short_code' => 'test123']);

        $alternatives = $this->service->suggestAlternativeCodes('test123');

        $this->assertIsArray($alternatives);
        $this->assertNotEmpty($alternatives);
        
        foreach ($alternatives as $alternative) {
            $this->assertTrue($this->service->isCodeAvailable($alternative));
        }
    }

    /** @test */
    public function it_can_generate_url_from_short_code()
    {
        $shortCode = 'abc123';
        $baseUrl = config('app.url');
        
        $url = $this->service->generateUrl($shortCode);
        
        $this->assertEquals("{$baseUrl}/{$shortCode}", $url);
    }

    /** @test */
    public function it_can_generate_url_with_custom_domain()
    {
        $shortCode = 'abc123';
        $customDomain = 'qr.example.com';
        
        $url = $this->service->generateUrl($shortCode, $customDomain);
        
        $this->assertEquals("https://{$customDomain}/{$shortCode}", $url);
    }

    /** @test */
    public function it_handles_reserved_codes()
    {
        $reservedCodes = [
            'admin',
            'api',
            'www',
            'mail',
            'ftp',
            'support',
            'help',
            'login',
            'register',
            'dashboard'
        ];

        foreach ($reservedCodes as $code) {
            $this->assertFalse($this->service->validateCustomCode($code));
        }
    }
}
