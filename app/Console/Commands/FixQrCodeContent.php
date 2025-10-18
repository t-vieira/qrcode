<?php

namespace App\Console\Commands;

use App\Models\QrCode;
use Illuminate\Console\Command;

class FixQrCodeContent extends Command
{
    protected $signature = 'qrcodes:fix-content';
    protected $description = 'Fix QR Code content from array to string format';

    public function handle()
    {
        $this->info('🔧 Corrigindo conteúdo dos QR Codes...');

        $qrCodes = QrCode::whereRaw('JSON_VALID(content) = 1')->get();

        if ($qrCodes->isEmpty()) {
            $this->info('✅ Nenhum QR Code com conteúdo em array encontrado.');
            return Command::SUCCESS;
        }

        $this->info("📊 Encontrados {$qrCodes->count()} QR Code(s) para corrigir.");

        $bar = $this->output->createProgressBar($qrCodes->count());
        $bar->start();

        $successCount = 0;

        foreach ($qrCodes as $qrCode) {
            try {
                $content = $qrCode->content;
                
                if (is_array($content)) {
                    // Converter array para string baseado no tipo
                    $newContent = match ($qrCode->type) {
                        'url' => $content['url'] ?? '',
                        'text' => $content['text'] ?? '',
                        'email' => $this->generateEmailString($content),
                        'phone' => $content['number'] ?? '',
                        'sms' => $content['number'] ?? '',
                        'wifi' => $this->generateWifiString($content),
                        default => $content['url'] ?? $content['text'] ?? '',
                    };
                    
                    $qrCode->update(['content' => $newContent]);
                    $successCount++;
                }
            } catch (\Exception $e) {
                $this->error("\n❌ Erro ao processar QR Code ID {$qrCode->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✅ Correção concluída!");
        $this->info("📈 QR Codes corrigidos: {$successCount}");

        return Command::SUCCESS;
    }

    protected function generateEmailString(array $content): string
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

    protected function generateWifiString(array $content): string
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
}
