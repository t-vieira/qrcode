<?php

namespace App\Jobs;

use App\Models\QrCode;
use App\Services\QrCodeGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateQrCodeFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected QrCode $qrCode;
    protected array $options;

    /**
     * Create a new job instance.
     */
    public function __construct(QrCode $qrCode, array $options = [])
    {
        $this->qrCode = $qrCode;
        $this->options = $options;
    }

    /**
     * Execute the job.
     */
    public function handle(QrCodeGeneratorService $qrGenerator): void
    {
        try {
            // Gerar o QR Code
            $qrCodeData = $qrGenerator->generate(
                $this->getQrCodeContent(),
                $this->getGenerationOptions()
            );

            // Salvar o arquivo
            $filePath = $this->saveQrCodeFile($qrCodeData);

            // Atualizar o modelo com o caminho do arquivo
            $this->qrCode->update([
                'file_path' => $filePath,
                'status' => 'active'
            ]);

            Log::info('QR Code file generated successfully', [
                'qr_code_id' => $this->qrCode->id,
                'file_path' => $filePath
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate QR Code file', [
                'qr_code_id' => $this->qrCode->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Marcar como falha
            $this->qrCode->update(['status' => 'failed']);

            throw $e;
        }
    }

    /**
     * Obter conteúdo do QR Code
     */
    private function getQrCodeContent(): string
    {
        $content = $this->qrCode->content;
        $type = $this->qrCode->type;

        switch ($type) {
            case 'url':
                return $content['url'] ?? '';
            case 'vcard':
                return $this->generateVCardContent($content);
            case 'wifi':
                return $this->generateWifiContent($content);
            case 'email':
                return $this->generateEmailContent($content);
            case 'phone':
                return "tel:{$content['phone']}";
            case 'sms':
                return "sms:{$content['phone']}:{$content['message']}";
            case 'text':
                return $content['text'] ?? '';
            default:
                return json_encode($content);
        }
    }

    /**
     * Gerar conteúdo vCard
     */
    private function generateVCardContent(array $content): string
    {
        $vcard = "BEGIN:VCARD\n";
        $vcard .= "VERSION:3.0\n";
        $vcard .= "FN:{$content['first_name']} {$content['last_name']}\n";
        $vcard .= "N:{$content['last_name']};{$content['first_name']};;;\n";
        
        if (!empty($content['organization'])) {
            $vcard .= "ORG:{$content['organization']}\n";
        }
        
        if (!empty($content['title'])) {
            $vcard .= "TITLE:{$content['title']}\n";
        }
        
        if (!empty($content['phone'])) {
            $vcard .= "TEL:{$content['phone']}\n";
        }
        
        if (!empty($content['email'])) {
            $vcard .= "EMAIL:{$content['email']}\n";
        }
        
        if (!empty($content['website'])) {
            $vcard .= "URL:{$content['website']}\n";
        }
        
        if (!empty($content['address'])) {
            $vcard .= "ADR:;;{$content['address']};{$content['city']};{$content['state']};{$content['zip']};{$content['country']}\n";
        }
        
        $vcard .= "END:VCARD";
        
        return $vcard;
    }

    /**
     * Gerar conteúdo Wi-Fi
     */
    private function generateWifiContent(array $content): string
    {
        $wifi = "WIFI:T:{$content['security']};S:{$content['ssid']};P:{$content['password']};";
        
        if (!empty($content['hidden']) && $content['hidden']) {
            $wifi .= "H:true;";
        }
        
        $wifi .= ";";
        
        return $wifi;
    }

    /**
     * Gerar conteúdo de email
     */
    private function generateEmailContent(array $content): string
    {
        $email = "mailto:{$content['email']}";
        
        $params = [];
        if (!empty($content['subject'])) {
            $params[] = "subject=" . urlencode($content['subject']);
        }
        if (!empty($content['body'])) {
            $params[] = "body=" . urlencode($content['body']);
        }
        
        if (!empty($params)) {
            $email .= "?" . implode('&', $params);
        }
        
        return $email;
    }

    /**
     * Obter opções de geração
     */
    private function getGenerationOptions(): array
    {
        $design = $this->qrCode->design ?? [];
        
        return array_merge([
            'size' => $this->qrCode->resolution ?? 300,
            'format' => $this->qrCode->format ?? 'png',
            'margin' => 10,
        ], $design, $this->options);
    }

    /**
     * Salvar arquivo do QR Code
     */
    private function saveQrCodeFile(string $qrCodeData): string
    {
        $fileName = $this->generateFileName();
        $filePath = "qrcodes/{$this->qrCode->user_id}/{$fileName}";
        
        Storage::disk('public')->put($filePath, $qrCodeData);
        
        return $filePath;
    }

    /**
     * Gerar nome do arquivo
     */
    private function generateFileName(): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $format = $this->qrCode->format ?? 'png';
        
        return "qr_{$this->qrCode->id}_{$timestamp}.{$format}";
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('QR Code generation job failed', [
            'qr_code_id' => $this->qrCode->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Notificar o usuário sobre a falha
        // Aqui você pode enviar um email ou notificação
    }
}