<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeGeneratorService
{
    protected PngWriter $pngWriter;
    protected SvgWriter $svgWriter;
    protected QrCodeDesignService $designService;

    public function __construct()
    {
        $this->pngWriter = new PngWriter();
        $this->svgWriter = new SvgWriter();
        $this->designService = new QrCodeDesignService();
    }

    /**
     * Gerar QR Code e salvar como arquivo
     */
    public function generateAndSave(string $content, string $filename, string $format = 'svg', array $design = null): string
    {
        // Se design personalizado foi especificado, usar o serviço de design
        if ($design && !empty($design)) {
            return $this->designService->saveCustomQrCode($content, $design, $filename, $format);
        }
        
        // Geração básica com resolução otimizada para downloads
        $qrCode = new QrCode(
            data: $content,
            size: $this->getOptimalResolution(), // Resolução otimizada baseada no ambiente
            margin: 20
        );
        
        // Verificar se GD está disponível para JPG
        if (in_array($format, ['jpg', 'jpeg']) && !extension_loaded('gd')) {
            $format = 'png'; // Fallback para PNG se GD não estiver disponível para JPG
        }
        
        // Escolher o writer baseado no formato
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        
        // Gerar o resultado
        $result = $writer->write($qrCode);
        
        // Definir o caminho do arquivo
        $filePath = 'qrcodes/' . $filename . '.' . $format;
        
        // Para JPG, converter PNG para JPG usando GD nativo
        if (in_array($format, ['jpg', 'jpeg'])) {
            if (!extension_loaded('gd')) {
                // Se GD não estiver disponível, salvar como PNG
                $filePath = 'qrcodes/' . $filename . '.png';
                Storage::disk('public')->put($filePath, $result->getString());
            } else {
                $pngPath = 'qrcodes/' . $filename . '.png';
                Storage::disk('public')->put($pngPath, $result->getString());
                
                // Converter PNG para JPG com GD nativo
                $pngData = Storage::disk('public')->get($pngPath);
                $image = imagecreatefromstring($pngData);
                
                if ($image !== false) {
                    // Criar fundo branco para JPG
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $jpgImage = imagecreatetruecolor($width, $height);
                    $white = imagecolorallocate($jpgImage, 255, 255, 255);
                    imagefill($jpgImage, 0, 0, $white);
                    
                    // Copiar PNG para JPG
                    imagecopy($jpgImage, $image, 0, 0, 0, 0, $width, $height);
                    
                    // Capturar JPG em buffer
                    ob_start();
                    imagejpeg($jpgImage, null, 100); // 100% de qualidade para máxima resolução
                    $jpgData = ob_get_contents();
                    ob_end_clean();
                    
                    // Salvar como JPG
                    Storage::disk('public')->put($filePath, $jpgData);
                    
                    // Limpar memória
                    imagedestroy($image);
                    imagedestroy($jpgImage);
                    
                    // Remover arquivo PNG temporário
                    Storage::disk('public')->delete($pngPath);
                } else {
                    // Fallback: salvar como PNG se conversão falhar
                    Storage::disk('public')->put($filePath, $result->getString());
                }
            }
        } else {
            // Salvar o arquivo normalmente
            Storage::disk('public')->put($filePath, $result->getString());
        }
        
        return $filePath;
    }

    /**
     * Gerar QR Code e retornar como string base64
     */
    public function generateBase64(string $content, string $format = 'svg'): string
    {
        $qrCode = new QrCode(
            data: $content,
            size: 2000, // Resolução alta para previews
            margin: 20
        );
        
        // Verificar se GD está disponível para JPG
        if (in_array($format, ['jpg', 'jpeg']) && !extension_loaded('gd')) {
            $format = 'png'; // Fallback para PNG se GD não estiver disponível para JPG
        }
        
        // Escolher o writer baseado no formato
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        
        // Gerar o resultado
        $result = $writer->write($qrCode);
        
        // Para JPG, converter PNG para JPG usando GD nativo
        if (in_array($format, ['jpg', 'jpeg'])) {
            if (!extension_loaded('gd')) {
                // Se GD não estiver disponível, retornar PNG
                return 'data:image/png;base64,' . base64_encode($result->getString());
            } else {
                $pngData = $result->getString();
                $image = imagecreatefromstring($pngData);
                
                if ($image !== false) {
                    // Criar fundo branco para JPG
                    $width = imagesx($image);
                    $height = imagesy($image);
                    $jpgImage = imagecreatetruecolor($width, $height);
                    $white = imagecolorallocate($jpgImage, 255, 255, 255);
                    imagefill($jpgImage, 0, 0, $white);
                    
                    // Copiar PNG para JPG
                    imagecopy($jpgImage, $image, 0, 0, 0, 0, $width, $height);
                    
                    // Capturar JPG em buffer
                    ob_start();
                    imagejpeg($jpgImage, null, 100); // 100% de qualidade para máxima resolução
                    $jpgData = ob_get_contents();
                    ob_end_clean();
                    
                    // Limpar memória
                    imagedestroy($image);
                    imagedestroy($jpgImage);
                    
                    return 'data:image/jpeg;base64,' . base64_encode($jpgData);
                } else {
                    // Fallback: retornar PNG se conversão falhar
                    return 'data:image/png;base64,' . base64_encode($pngData);
                }
            }
        }
        
        return 'data:image/' . $format . '+xml;base64,' . base64_encode($result->getString());
    }

    /**
     * Gerar nome único para arquivo
     */
    public function generateUniqueFilename(): string
    {
        return Str::random(12) . '_' . time();
    }

    /**
     * Gerar QR Code para download com resolução otimizada
     */
    public function generateHighResolutionDownload(string $content, string $format = 'png', array $design = null): string
    {
        // Detectar ambiente e ajustar resolução automaticamente
        $size = $this->getOptimalResolution();
        $margin = 20;
        
        // Se design personalizado foi especificado, usar o serviço de design
        if ($design && !empty($design)) {
            $design['size'] = $size;
            $design['margin'] = $margin;
            return $this->designService->generateWithDesign($content, $design, $format)->getString();
        }
        
        // Geração básica com resolução ultra alta
        $qrCode = new QrCode(
            data: $content,
            size: $size,
            margin: $margin
        );
        
        // Verificar se GD está disponível para JPG
        if (in_array($format, ['jpg', 'jpeg']) && !extension_loaded('gd')) {
            $format = 'png'; // Fallback para PNG se GD não estiver disponível para JPG
        }
        
        // Escolher o writer baseado no formato
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        
        // Gerar o resultado
        $result = $writer->write($qrCode);
        $data = $result->getString();
        
        // Limpar memória
        unset($qrCode, $result);
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        return $data;
    }

    /**
     * Obter resolução ótima baseada no ambiente e memória disponível
     */
    private function getOptimalResolution(): int
    {
        // Verificar memória disponível
        $memoryLimit = $this->parseMemoryLimit(ini_get('memory_limit'));
        $memoryUsage = memory_get_usage(true);
        $availableMemory = $memoryLimit - $memoryUsage;
        
        // Verificar se é ambiente de produção
        $isProduction = app()->environment('production');
        
        // Ajustar resolução baseada na memória disponível
        if ($availableMemory < 50 * 1024 * 1024) { // Menos de 50MB disponível
            return 1500; // Resolução moderada
        } elseif ($availableMemory < 100 * 1024 * 1024) { // Menos de 100MB disponível
            return 2000; // Resolução alta
        } elseif ($isProduction) {
            return 2500; // Resolução alta para produção
        } else {
            return 3000; // Resolução ultra alta para desenvolvimento
        }
    }
    
    /**
     * Converter limite de memória para bytes
     */
    private function parseMemoryLimit(string $memoryLimit): int
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $memoryLimit = (int) $memoryLimit;
        
        switch ($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }
        
        return $memoryLimit;
    }

    /**
     * Obter URL do QR Code
     */
    public function getQrCodeUrl(string $filePath): string
    {
        return Storage::disk('public')->url($filePath);
    }

    /**
     * Deletar arquivo do QR Code
     */
    public function deleteQrCodeFile(string $filePath): bool
    {
        if (Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->delete($filePath);
        }
        
        return false;
    }
}