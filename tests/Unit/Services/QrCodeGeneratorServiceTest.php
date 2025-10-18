<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\QrCodeGeneratorService;
use App\Models\User;
use App\Models\QrCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class QrCodeGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected QrCodeGeneratorService $service;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new QrCodeGeneratorService();
        $this->user = User::factory()->create();
        
        Storage::fake('public');
    }

    /** @test */
    public function it_can_generate_qr_code_with_basic_content()
    {
        $content = 'https://example.com';
        $options = [
            'size' => 300,
            'margin' => 10,
            'format' => 'png'
        ];

        $result = $this->service->generate($content, $options);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /** @test */
    public function it_can_generate_qr_code_with_custom_colors()
    {
        $content = 'https://example.com';
        $options = [
            'size' => 300,
            'foreground_color' => ['r' => 255, 'g' => 0, 'b' => 0],
            'background_color' => ['r' => 255, 'g' => 255, 'b' => 255],
            'format' => 'png'
        ];

        $result = $this->service->generate($content, $options);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /** @test */
    public function it_can_generate_qr_code_with_logo()
    {
        $content = 'https://example.com';
        $logo = UploadedFile::fake()->image('logo.png', 100, 100);
        
        $options = [
            'size' => 300,
            'logo' => $logo,
            'logo_size' => 50,
            'format' => 'png'
        ];

        $result = $this->service->generate($content, $options);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /** @test */
    public function it_can_generate_qr_code_in_different_formats()
    {
        $content = 'https://example.com';
        $formats = ['png', 'jpg', 'svg', 'eps'];

        foreach ($formats as $format) {
            $options = [
                'size' => 300,
                'format' => $format
            ];

            $result = $this->service->generate($content, $options);

            $this->assertIsString($result);
            $this->assertNotEmpty($result);
        }
    }

    /** @test */
    public function it_can_generate_qr_code_with_different_sizes()
    {
        $content = 'https://example.com';
        $sizes = [100, 200, 500, 1000];

        foreach ($sizes as $size) {
            $options = [
                'size' => $size,
                'format' => 'png'
            ];

            $result = $this->service->generate($content, $options);

            $this->assertIsString($result);
            $this->assertNotEmpty($result);
        }
    }

    /** @test */
    public function it_throws_exception_for_invalid_content()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->service->generate('', ['size' => 300]);
    }

    /** @test */
    public function it_throws_exception_for_invalid_size()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->service->generate('https://example.com', ['size' => 0]);
    }

    /** @test */
    public function it_throws_exception_for_invalid_format()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->service->generate('https://example.com', [
            'size' => 300,
            'format' => 'invalid'
        ]);
    }

    /** @test */
    public function it_can_download_qr_code()
    {
        $content = 'https://example.com';
        $options = [
            'size' => 300,
            'format' => 'png'
        ];

        $response = $this->service->download($content, $options, 'test-qr.png');

        $this->assertInstanceOf(\Symfony\Component\HttpFoundation\BinaryFileResponse::class, $response);
    }

    /** @test */
    public function it_can_generate_preview()
    {
        $content = 'https://example.com';
        $options = [
            'size' => 300,
            'format' => 'png'
        ];

        $result = $this->service->generatePreview($content, $options);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /** @test */
    public function it_handles_large_content()
    {
        $content = str_repeat('https://example.com/', 100);
        $options = [
            'size' => 300,
            'format' => 'png'
        ];

        $result = $this->service->generate($content, $options);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /** @test */
    public function it_handles_special_characters()
    {
        $content = 'https://example.com/áéíóú-ç-ñ-ã';
        $options = [
            'size' => 300,
            'format' => 'png'
        ];

        $result = $this->service->generate($content, $options);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /** @test */
    public function it_handles_unicode_content()
    {
        $content = 'https://example.com/测试-тест-اختبار';
        $options = [
            'size' => 300,
            'format' => 'png'
        ];

        $result = $this->service->generate($content, $options);

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }
}
