<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

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
        
        // Geração básica com alta resolução para downloads
        $qrCode = new QrCode(
            data: $content,
            size: 2000, // Resolução alta para máxima qualidade
            margin: 10
        );
        
        // Verificar se GD está disponível para PNG/JPG
        if (in_array($format, ['png', 'jpg']) && !extension_loaded('gd')) {
            $format = 'svg'; // Fallback para SVG se GD não estiver disponível
        }
        
        // Escolher o writer baseado no formato
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        
        // Gerar o resultado
        $result = $writer->write($qrCode);
        
        // Definir o caminho do arquivo
        $filePath = 'qrcodes/' . $filename . '.' . $format;
        
        // Para JPG, converter PNG para JPG usando Intervention Image
        if (in_array($format, ['jpg', 'jpeg'])) {
            $pngPath = 'qrcodes/' . $filename . '.png';
            Storage::disk('public')->put($pngPath, $result->getString());
            
            // Converter PNG para JPG com alta qualidade
            $image = Image::make(Storage::disk('public')->path($pngPath));
            $image->encode('jpg', 95); // 95% de qualidade
            
            // Salvar como JPG
            Storage::disk('public')->put($filePath, $image->stream());
            
            // Remover arquivo PNG temporário
            Storage::disk('public')->delete($pngPath);
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
            size: 1000, // Resolução para previews
            margin: 10
        );
        
        // Verificar se GD está disponível para PNG/JPG
        if (in_array($format, ['png', 'jpg']) && !extension_loaded('gd')) {
            $format = 'svg'; // Fallback para SVG se GD não estiver disponível
        }
        
        // Escolher o writer baseado no formato
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        
        // Gerar o resultado
        $result = $writer->write($qrCode);
        
        // Para JPG, converter PNG para JPG
        if (in_array($format, ['jpg', 'jpeg'])) {
            $image = Image::make($result->getString());
            $image->encode('jpg', 95);
            return 'data:image/jpeg;base64,' . base64_encode($image->stream());
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