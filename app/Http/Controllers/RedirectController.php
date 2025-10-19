<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\QrScan;
use App\Services\QrScanTracker;
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
        // Log para debug
        \Log::info('QR Code redirect called', [
            'short_code' => $shortCode,
            'request_url' => $request->url(),
            'user_agent' => $request->userAgent(),
            'ip_address' => $request->ip(),
            'request_method' => $request->method(),
            'request_headers' => $request->headers->all()
        ]);

        // Buscar o QR Code pelo short_code
        \Log::info('Searching for QR code', [
            'short_code' => $shortCode,
            'total_qr_codes' => QrCode::count(),
            'active_qr_codes' => QrCode::where('status', 'active')->count()
        ]);

        $qrCode = QrCode::where('short_code', $shortCode)
            ->where('status', 'active')
            ->first();

        if (!$qrCode) {
            // Log mais detalhado para debug
            $allQrCodes = QrCode::select('id', 'name', 'short_code', 'status', 'user_id')->get();
            $activeQrCodes = QrCode::where('status', 'active')->select('id', 'name', 'short_code', 'user_id')->get();
            
            \Log::warning('QR Code not found for redirect', [
                'short_code' => $shortCode,
                'total_qr_codes' => QrCode::count(),
                'active_qr_codes' => QrCode::where('status', 'active')->count(),
                'all_qr_codes' => $allQrCodes->toArray(),
                'active_qr_codes_list' => $activeQrCodes->toArray(),
                'similar_short_codes' => QrCode::where('short_code', 'like', '%' . substr($shortCode, 0, 3) . '%')->pluck('short_code', 'id')->toArray()
            ]);
            
            abort(404, 'QR Code não encontrado ou inativo.');
        }

        \Log::info('QR Code found for redirect', [
            'qr_code_id' => $qrCode->id,
            'qr_code_name' => $qrCode->name,
            'qr_code_type' => $qrCode->type,
            'qr_code_content' => $qrCode->content,
            'qr_code_user_id' => $qrCode->user_id
        ]);

        // Registrar o scan
        $this->recordScan($qrCode, $request);

        // Obter o conteúdo para redirecionamento
        $content = $this->getRedirectContent($qrCode);

        \Log::info('QR Code content extracted', [
            'qr_code_id' => $qrCode->id,
            'content' => $content,
            'content_type' => gettype($content),
            'content_length' => strlen($content)
        ]);

        if (empty($content)) {
            \Log::error('QR Code content is empty', [
                'qr_code_id' => $qrCode->id,
                'qr_code_type' => $qrCode->type,
                'qr_code_content_raw' => $qrCode->content
            ]);
            abort(404, 'Conteúdo do QR Code não encontrado.');
        }

        // Redirecionar baseado no tipo
        \Log::info('Performing redirect', [
            'qr_code_id' => $qrCode->id,
            'redirect_type' => $qrCode->type,
            'redirect_content' => $content
        ]);

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
        \Log::info('Performing redirect match', [
            'type' => $type,
            'content' => $content,
            'content_length' => strlen($content)
        ]);

        $redirectResponse = match ($type) {
            'url' => redirect($content),
            'email' => redirect($content),
            'phone' => redirect($content),
            'sms' => redirect($content),
            'wifi' => redirect($content),
            'text' => redirect()->route('qr.text', ['content' => base64_encode($content)]),
            default => redirect($content),
        };

        \Log::info('Redirect response created', [
            'type' => $type,
            'redirect_url' => $redirectResponse->getTargetUrl(),
            'status_code' => $redirectResponse->getStatusCode()
        ]);

        return $redirectResponse;
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
     * Registrar scan do QR Code usando QrScanTracker
     */
    protected function recordScan(QrCode $qrCode, Request $request): void
    {
        try {
            // Usar o QrScanTracker que tem rastreamento completo de país
            $tracker = new QrScanTracker();
            $tracker->track($qrCode, $request);
            
        } catch (\Exception $e) {
            // Log do erro mas não interromper o redirecionamento
            \Log::error('Erro ao registrar scan do QR Code', [
                'qr_code_id' => $qrCode->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}