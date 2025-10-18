<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\QrScanTracker;
use App\Models\QrCode;
use App\Models\QrScan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;

class QrScanTrackerTest extends TestCase
{
    use RefreshDatabase;

    protected QrScanTracker $service;
    protected User $user;
    protected QrCode $qrCode;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new QrScanTracker();
        $this->user = User::factory()->create();
        $this->qrCode = QrCode::factory()->create(['user_id' => $this->user->id]);
    }

    /** @test */
    public function it_can_track_scan()
    {
        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'REMOTE_ADDR' => '192.168.1.1'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);

        $this->assertInstanceOf(QrScan::class, $scan);
        $this->assertEquals($this->qrCode->id, $scan->qr_code_id);
        $this->assertEquals('192.168.1.1', $scan->ip_address);
        $this->assertTrue($scan->is_unique);
    }

    /** @test */
    public function it_detects_device_type()
    {
        $mobileUserAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15';
        $desktopUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $tabletUserAgent = 'Mozilla/5.0 (iPad; CPU OS 14_0 like Mac OS X) AppleWebKit/605.1.15';

        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $mobileUserAgent,
            'REMOTE_ADDR' => '192.168.1.1'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);
        $this->assertEquals('mobile', $scan->device_type);

        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $desktopUserAgent,
            'REMOTE_ADDR' => '192.168.1.2'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);
        $this->assertEquals('desktop', $scan->device_type);

        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $tabletUserAgent,
            'REMOTE_ADDR' => '192.168.1.3'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);
        $this->assertEquals('tablet', $scan->device_type);
    }

    /** @test */
    public function it_detects_browser()
    {
        $chromeUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
        $firefoxUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0';
        $safariUserAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15';

        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $chromeUserAgent,
            'REMOTE_ADDR' => '192.168.1.1'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);
        $this->assertEquals('Chrome', $scan->browser);

        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $firefoxUserAgent,
            'REMOTE_ADDR' => '192.168.1.2'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);
        $this->assertEquals('Firefox', $scan->browser);

        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $safariUserAgent,
            'REMOTE_ADDR' => '192.168.1.3'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);
        $this->assertEquals('Safari', $scan->browser);
    }

    /** @test */
    public function it_detects_operating_system()
    {
        $windowsUserAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36';
        $macUserAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15';
        $linuxUserAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36';

        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $windowsUserAgent,
            'REMOTE_ADDR' => '192.168.1.1'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);
        $this->assertEquals('Windows', $scan->os);

        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $macUserAgent,
            'REMOTE_ADDR' => '192.168.1.2'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);
        $this->assertEquals('macOS', $scan->os);

        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $linuxUserAgent,
            'REMOTE_ADDR' => '192.168.1.3'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);
        $this->assertEquals('Linux', $scan->os);
    }

    /** @test */
    public function it_marks_duplicate_scans_as_not_unique()
    {
        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'REMOTE_ADDR' => '192.168.1.1'
        ]);

        // First scan should be unique
        $firstScan = $this->service->trackScan($this->qrCode, $request);
        $this->assertTrue($firstScan->is_unique);

        // Second scan from same IP should not be unique
        $secondScan = $this->service->trackScan($this->qrCode, $request);
        $this->assertFalse($secondScan->is_unique);
    }

    /** @test */
    public function it_handles_missing_user_agent()
    {
        $request = Request::create('/test', 'GET', [], [], [], [
            'REMOTE_ADDR' => '192.168.1.1'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);

        $this->assertInstanceOf(QrScan::class, $scan);
        $this->assertEquals('192.168.1.1', $scan->ip_address);
        $this->assertNull($scan->browser);
        $this->assertNull($scan->os);
        $this->assertNull($scan->device_type);
    }

    /** @test */
    public function it_handles_missing_ip_address()
    {
        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);

        $this->assertInstanceOf(QrScan::class, $scan);
        $this->assertNull($scan->ip_address);
    }

    /** @test */
    public function it_updates_qr_code_scan_count()
    {
        $initialCount = $this->qrCode->scans_count;
        
        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'REMOTE_ADDR' => '192.168.1.1'
        ]);

        $this->service->trackScan($this->qrCode, $request);

        $this->qrCode->refresh();
        $this->assertEquals($initialCount + 1, $this->qrCode->scans_count);
    }

    /** @test */
    public function it_handles_geolocation_data()
    {
        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'REMOTE_ADDR' => '8.8.8.8' // Google DNS for testing
        ]);

        $scan = $this->service->trackScan($this->qrCode, $request);

        $this->assertInstanceOf(QrScan::class, $scan);
        // Note: In real implementation, this would call a geolocation API
        // For testing, we just verify the scan was created
    }

    /** @test */
    public function it_handles_multiple_qr_codes()
    {
        $qrCode2 = QrCode::factory()->create(['user_id' => $this->user->id]);
        
        $request = Request::create('/test', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'REMOTE_ADDR' => '192.168.1.1'
        ]);

        $scan1 = $this->service->trackScan($this->qrCode, $request);
        $scan2 = $this->service->trackScan($qrCode2, $request);

        $this->assertEquals($this->qrCode->id, $scan1->qr_code_id);
        $this->assertEquals($qrCode2->id, $scan2->qr_code_id);
        $this->assertTrue($scan1->is_unique);
        $this->assertTrue($scan2->is_unique); // Different QR codes
    }
}
