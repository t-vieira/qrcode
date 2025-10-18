<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\Result\ResultInterface;
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
    public function generateAndSave(string $content, string $filename, string $format = 'png'): string
    {
        $qrCode = $this->createQrCode($content);
        
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
    public function generateBase64(string $content, string $format = 'png'): string
    {
        $qrCode = $this->createQrCode($content);
        
        // Escolher o writer baseado no formato
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        
        // Gerar o resultado
        $result = $writer->write($qrCode);
        
        return 'data:image/' . $format . ';base64,' . base64_encode($result->getString());
    }

    /**
     * Criar instância do QR Code com configurações padrão
     */
    protected function createQrCode(string $content): QrCode
    {
        return QrCode::create($content)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
    }

    /**
     * Gerar QR Code com configurações customizadas
     */
    public function generateCustom(
        string $content,
        int $size = 300,
        string $foregroundColor = '#000000',
        string $backgroundColor = '#FFFFFF',
        int $margin = 10
    ): QrCode {
        return QrCode::create($content)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize($size)
            ->setMargin($margin)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(
                hexdec(substr($foregroundColor, 1, 2)),
                hexdec(substr($foregroundColor, 3, 2)),
                hexdec(substr($foregroundColor, 5, 2))
            ))
            ->setBackgroundColor(new Color(
                hexdec(substr($backgroundColor, 1, 2)),
                hexdec(substr($backgroundColor, 3, 2)),
                hexdec(substr($backgroundColor, 5, 2))
            ));
    }

    /**
     * Gerar QR Code com logo
     */
    public function generateWithLogo(
        string $content,
        string $logoPath,
        int $logoSize = 60,
        string $format = 'png'
    ): string {
        $qrCode = $this->createQrCode($content);
        
        // Adicionar logo se existir
        if (Storage::disk('public')->exists($logoPath)) {
            $logoData = Storage::disk('public')->get($logoPath);
            $qrCode->setLogoPath($logoPath);
            $qrCode->setLogoSize($logoSize);
        }
        
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        $result = $writer->write($qrCode);
        
        return 'data:image/' . $format . ';base64,' . base64_encode($result->getString());
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