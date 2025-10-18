<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Services\QrScanTracker;
use App\Services\QueueService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class RedirectController extends Controller
{
    protected QrScanTracker $scanTracker;
    protected QueueService $queueService;
    protected CacheService $cacheService;

    public function __construct(
        QrScanTracker $scanTracker,
        QueueService $queueService,
        CacheService $cacheService
    ) {
        $this->scanTracker = $scanTracker;
        $this->queueService = $queueService;
        $this->cacheService = $cacheService;
    }

    public function redirect(string $shortCode, Request $request): RedirectResponse
    {
        // Buscar o QR Code pelo short_code
        $qrCode = QrCode::where('short_code', $shortCode)
            ->where('status', 'active')
            ->first();

        if (!$qrCode) {
            abort(404, 'QR Code não encontrado ou inativo.');
        }

        // Processar scan em fila para melhor performance
        $scanData = $this->prepareScanData($request);
        $this->queueService->processQrCodeScan($qrCode, $scanData);
        
        // Incrementar contador em cache para tempo real
        $this->cacheService->incrementScanCount($qrCode);

        // Obter o conteúdo para redirecionamento
        $content = $this->getRedirectContent($qrCode);

        if (empty($content)) {
            abort(404, 'Conteúdo do QR Code não encontrado.');
        }

        // Redirecionar baseado no tipo
        return $this->performRedirect($qrCode->type, $content);
    }

    protected function getRedirectContent(QrCode $qrCode): string
    {
        $typeClass = "App\\Services\\QrTypes\\" . ucfirst($qrCode->type) . "QrType";
        
        if (class_exists($typeClass)) {
            $typeInstance = new $typeClass;
            if ($typeInstance instanceof \App\Services\QrTypes\QrTypeInterface) {
                return $typeInstance->generateContent($qrCode->content);
            }
        }

        // Fallback para tipos básicos
        return match ($qrCode->type) {
            'url' => $qrCode->content['url'] ?? '',
            'text' => $qrCode->content['text'] ?? '',
            'email' => $this->generateEmailContent($qrCode->content),
            'phone' => 'tel:' . ($qrCode->content['number'] ?? ''),
            'sms' => 'sms:' . ($qrCode->content['number'] ?? '') . ':' . ($qrCode->content['message'] ?? ''),
            'wifi' => $this->generateWifiContent($qrCode->content),
            default => $qrCode->content['url'] ?? $qrCode->content['text'] ?? '',
        };
    }

    protected function performRedirect(string $type, string $content): RedirectResponse
    {
        return match ($type) {
            'url' => redirect($content),
            'email' => redirect($content),
            'phone' => redirect($content),
            'sms' => redirect($content),
            'wifi' => redirect($content),
            'text' => redirect()->route('qr.text', ['content' => base64_encode($content)]),
            default => redirect($content),
        };
    }

    protected function generateEmailContent(array $content): string
    {
        $to = $content['to'] ?? '';
        $subject = $content['subject'] ?? '';
        $body = $content['body'] ?? '';
        
        $mailto = "mailto:{$to}";
        
        $params = [];
        if ($subject) {
            $params[] = "subject=" . urlencode($subject);
        }
        if ($body) {
            $params[] = "body=" . urlencode($body);
        }
        
        if (!empty($params)) {
            $mailto .= '?' . implode('&', $params);
        }
        
        return $mailto;
    }

    protected function generateWifiContent(array $content): string
    {
        $ssid = $content['ssid'] ?? '';
        $password = $content['password'] ?? '';
        $security = $content['security'] ?? 'WPA';
        $hidden = $content['hidden'] ?? false;
        
        $wifi = "WIFI:T:{$security};S:{$ssid};P:{$password}";
        
        if ($hidden) {
            $wifi .= ';H:true';
        }
        
        $wifi .= ';;';
        
        return $wifi;
    }

    public function showText(string $encodedContent)
    {
        $content = base64_decode($encodedContent);
        
        if (!$content) {
            abort(404, 'Conteúdo não encontrado.');
        }

        return view('qr.text', compact('content'));
    }

    /**
     * Preparar dados do scan para processamento em fila
     */
    protected function prepareScanData(Request $request): array
    {
        $userAgent = $request->userAgent();
        $ipAddress = $request->ip();
        
        // Detectar informações do dispositivo
        $deviceData = $this->cacheService->getDeviceData($userAgent);
        
        // Obter dados de geolocalização
        $locationData = $this->cacheService->getLocationData($ipAddress);
        
        return array_merge([
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
        ], $deviceData, $locationData);
    }
}