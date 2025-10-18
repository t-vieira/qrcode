<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\QrScan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Jenssegers\Agent\Agent;

class RedirectController extends Controller
{
    public function __construct()
    {
        // Sem dependências complexas por enquanto
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

        // Registrar o scan
        $this->recordScan($qrCode, $request);

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
        // Se content é string (novo formato), usar diretamente
        if (is_string($qrCode->content)) {
            return $qrCode->content;
        }

        // Se content é array (formato antigo), extrair baseado no tipo
        if (is_array($qrCode->content)) {
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

        return '';
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
     * Registrar scan do QR Code
     */
    protected function recordScan(QrCode $qrCode, Request $request): void
    {
        try {
            $agent = new Agent();
            $userAgent = $request->userAgent();
            $ipAddress = $request->ip();

            // Detectar informações do dispositivo
            $deviceType = 'desktop';
            if ($agent->isMobile()) {
                $deviceType = 'mobile';
            } elseif ($agent->isTablet()) {
                $deviceType = 'tablet';
            }

            $platform = $agent->platform() ?: 'Unknown';
            $browser = $agent->browser() ?: 'Unknown';

            // Verificar se é um scan único (mesmo IP + dispositivo nas últimas 24h)
            $isUnique = !QrScan::where('qr_code_id', $qrCode->id)
                ->where('ip_address', $ipAddress)
                ->where('device_type', $deviceType)
                ->where('scanned_at', '>=', now()->subDay())
                ->exists();

            // Criar registro do scan
            QrScan::create([
                'qr_code_id' => $qrCode->id,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'device_type' => $deviceType,
                'os' => $platform,
                'browser' => $browser,
                'country' => null, // Pode ser implementado com API de geolocalização
                'city' => null,
                'latitude' => null,
                'longitude' => null,
                'is_unique' => $isUnique,
                'scanned_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Log do erro mas não interromper o redirecionamento
            \Log::error('Erro ao registrar scan do QR Code', [
                'qr_code_id' => $qrCode->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}