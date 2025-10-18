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

    public function __construct()
    {
        $this->pngWriter = new PngWriter();
        $this->svgWriter = new SvgWriter();
    }

    /**
     * Gerar QR Code e salvar como arquivo
     */
    public function generateAndSave(string $content, string $filename, string $format = 'svg'): string
    {
        $qrCode = new QrCode($content);
        
        // Verificar se GD está disponível para PNG
        if ($format === 'png' && !extension_loaded('gd')) {
            $format = 'svg'; // Fallback para SVG se GD não estiver disponível
        }
        
        // Escolher o writer baseado no formato
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        
        // Gerar o resultado
        $result = $writer->write($qrCode);
        
        // Definir o caminho do arquivo
        $filePath = 'qrcodes/' . $filename . '.' . $format;
        
        // Salvar o arquivo
        Storage::disk('public')->put($filePath, $result->getString());
        
        return $filePath;
    }

    /**
     * Gerar QR Code e retornar como string base64
     */
    public function generateBase64(string $content, string $format = 'svg'): string
    {
        $qrCode = new QrCode($content);
        
        // Verificar se GD está disponível para PNG
        if ($format === 'png' && !extension_loaded('gd')) {
            $format = 'svg'; // Fallback para SVG se GD não estiver disponível
        }
        
        // Escolher o writer baseado no formato
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        
        // Gerar o resultado
        $result = $writer->write($qrCode);
        
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