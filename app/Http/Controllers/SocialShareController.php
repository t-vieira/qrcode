<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class SocialShareController extends Controller
{
    /**
     * Compartilhar QR Code via WhatsApp
     */
    public function whatsapp(Request $request, QrCode $qrCode): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário tem acesso ao QR Code
        if (!$user->can('view', $qrCode)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code não encontrado.',
            ], 404);
        }

        $shareUrl = $this->generateShareUrl($qrCode);
        $message = $this->generateWhatsAppMessage($qrCode, $shareUrl);
        
        $whatsappUrl = "https://wa.me/?text=" . urlencode($message);

        return response()->json([
            'success' => true,
            'url' => $whatsappUrl,
            'message' => $message,
        ]);
    }

    /**
     * Compartilhar QR Code via Facebook
     */
    public function facebook(Request $request, QrCode $qrCode): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário tem acesso ao QR Code
        if (!$user->can('view', $qrCode)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code não encontrado.',
            ], 404);
        }

        $shareUrl = $this->generateShareUrl($qrCode);
        $facebookUrl = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($shareUrl);

        return response()->json([
            'success' => true,
            'url' => $facebookUrl,
        ]);
    }

    /**
     * Compartilhar QR Code via Twitter
     */
    public function twitter(Request $request, QrCode $qrCode): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário tem acesso ao QR Code
        if (!$user->can('view', $qrCode)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code não encontrado.',
            ], 404);
        }

        $shareUrl = $this->generateShareUrl($qrCode);
        $text = "Confira este QR Code: {$qrCode->name}";
        $twitterUrl = "https://twitter.com/intent/tweet?text=" . urlencode($text) . "&url=" . urlencode($shareUrl);

        return response()->json([
            'success' => true,
            'url' => $twitterUrl,
        ]);
    }

    /**
     * Compartilhar QR Code via LinkedIn
     */
    public function linkedin(Request $request, QrCode $qrCode): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário tem acesso ao QR Code
        if (!$user->can('view', $qrCode)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code não encontrado.',
            ], 404);
        }

        $shareUrl = $this->generateShareUrl($qrCode);
        $linkedinUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($shareUrl);

        return response()->json([
            'success' => true,
            'url' => $linkedinUrl,
        ]);
    }

    /**
     * Compartilhar QR Code via Email
     */
    public function email(Request $request, QrCode $qrCode): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário tem acesso ao QR Code
        if (!$user->can('view', $qrCode)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code não encontrado.',
            ], 404);
        }

        $shareUrl = $this->generateShareUrl($qrCode);
        $subject = "QR Code: {$qrCode->name}";
        $body = $this->generateEmailBody($qrCode, $shareUrl);
        
        $emailUrl = "mailto:?subject=" . urlencode($subject) . "&body=" . urlencode($body);

        return response()->json([
            'success' => true,
            'url' => $emailUrl,
            'subject' => $subject,
            'body' => $body,
        ]);
    }

    /**
     * Gerar link de compartilhamento público
     */
    public function generatePublicLink(Request $request, QrCode $qrCode): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário tem acesso ao QR Code
        if (!$user->can('view', $qrCode)) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code não encontrado.',
            ], 404);
        }

        $shareUrl = $this->generateShareUrl($qrCode);

        return response()->json([
            'success' => true,
            'url' => $shareUrl,
            'qr_code' => [
                'id' => $qrCode->id,
                'name' => $qrCode->name,
                'type' => $qrCode->type,
                'created_at' => $qrCode->created_at,
            ],
        ]);
    }

    /**
     * Obter dados para Open Graph
     */
    public function openGraph(Request $request, QrCode $qrCode)
    {
        $user = $request->user();
        
        // Verificar se o usuário tem acesso ao QR Code
        if (!$user->can('view', $qrCode)) {
            abort(404);
        }

        $shareUrl = $this->generateShareUrl($qrCode);
        $imageUrl = $this->generateQrCodeImageUrl($qrCode);

        $ogData = [
            'title' => $qrCode->name,
            'description' => $this->generateDescription($qrCode),
            'url' => $shareUrl,
            'image' => $imageUrl,
            'type' => 'website',
            'site_name' => config('app.name'),
        ];

        return response()->view('social-share.open-graph', $ogData)
            ->header('Content-Type', 'text/html');
    }

    /**
     * Gerar URL de compartilhamento
     */
    protected function generateShareUrl(QrCode $qrCode): string
    {
        if ($qrCode->short_code) {
            return route('qr.redirect', ['shortCode' => $qrCode->short_code]);
        }

        return route('qrcodes.show', ['qrCode' => $qrCode->id]);
    }

    /**
     * Gerar mensagem para WhatsApp
     */
    protected function generateWhatsAppMessage(QrCode $qrCode, string $shareUrl): string
    {
        $message = "🔗 *{$qrCode->name}*\n\n";
        $message .= "Confira este QR Code que criei!\n\n";
        $message .= "📱 Escaneie o QR Code ou acesse o link:\n";
        $message .= $shareUrl . "\n\n";
        $message .= "Criado com " . config('app.name');

        return $message;
    }

    /**
     * Gerar corpo do email
     */
    protected function generateEmailBody(QrCode $qrCode, string $shareUrl): string
    {
        $body = "Olá!\n\n";
        $body .= "Gostaria de compartilhar este QR Code com você:\n\n";
        $body .= "Nome: {$qrCode->name}\n";
        $body .= "Tipo: " . ucfirst($qrCode->type) . "\n";
        $body .= "Link: {$shareUrl}\n\n";
        $body .= "Você pode escanear o QR Code ou acessar o link diretamente.\n\n";
        $body .= "Atenciosamente,\n";
        $body .= "Criado com " . config('app.name');

        return $body;
    }

    /**
     * Gerar descrição para Open Graph
     */
    protected function generateDescription(QrCode $qrCode): string
    {
        $descriptions = [
            'url' => 'QR Code que redireciona para uma URL',
            'vcard' => 'QR Code com informações de contato (vCard)',
            'text' => 'QR Code com texto personalizado',
            'email' => 'QR Code para envio de email',
            'phone' => 'QR Code para ligação telefônica',
            'sms' => 'QR Code para envio de SMS',
            'wifi' => 'QR Code para conexão Wi-Fi',
            'location' => 'QR Code com localização',
        ];

        return $descriptions[$qrCode->type] ?? 'QR Code personalizado';
    }

    /**
     * Gerar URL da imagem do QR Code
     */
    protected function generateQrCodeImageUrl(QrCode $qrCode): string
    {
        if ($qrCode->file_path && Storage::exists($qrCode->file_path)) {
            return Storage::url($qrCode->file_path);
        }

        // Gerar URL para preview do QR Code
        return route('qrcodes.preview', ['qrCode' => $qrCode->id]);
    }
}
