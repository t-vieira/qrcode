<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Color\Color;
use Intervention\Image\Facades\Image;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Illuminate\Support\Facades\Storage;

class QrCodeDesignService
{
    protected PngWriter $pngWriter;
    protected SvgWriter $svgWriter;

    public function __construct()
    {
        $this->pngWriter = new PngWriter();
        $this->svgWriter = new SvgWriter();
    }

    /**
     * Gerar QR Code com design personalizado
     */
    public function generateWithDesign(string $content, array $design, string $format = 'svg'): ResultInterface
    {
        // Configurar parâmetros com resolução otimizada para downloads
        $size = $design['size'] ?? $this->getOptimalResolution(); // Resolução otimizada por padrão
        $margin = $design['margin'] ?? 20;
        
        // Configurar cores
        $foregroundColor = new Color(0, 0, 0); // Padrão preto
        $backgroundColor = new Color(255, 255, 255); // Padrão branco
        
        if (isset($design['colors'])) {
            $colors = $design['colors'];
            
            // Cor do corpo (módulos)
            if (isset($colors['body'])) {
                $foregroundColor = $this->hexToColor($colors['body']);
            }
            
            // Cor de fundo
            if (isset($colors['background'])) {
                $backgroundColor = $this->hexToColor($colors['background']);
            }
        }
        
        // Configurar formato dos módulos
        $roundBlockSizeMode = RoundBlockSizeMode::Margin;
        if (isset($design['shape']) && $design['shape'] === 'square') {
            $roundBlockSizeMode = RoundBlockSizeMode::None;
        }
        
        // Criar QR Code com parâmetros
        $qrCode = new QrCode(
            data: $content,
            size: $size,
            margin: $margin,
            roundBlockSizeMode: $roundBlockSizeMode,
            foregroundColor: $foregroundColor,
            backgroundColor: $backgroundColor
        );
        
        // Adicionar logo se especificado
        if (isset($design['logo']) && !empty($design['logo'])) {
            $logo = Logo::create($design['logo'])
                ->setResizeToWidth($design['logo_size'] ?? 50)
                ->setResizeToHeight($design['logo_size'] ?? 50);
            $qrCode = $qrCode->withLogo($logo);
        }
        
        // Escolher writer baseado no formato
        $writer = $format === 'svg' ? $this->svgWriter : $this->pngWriter;
        
        return $writer->write($qrCode);
    }

    /**
     * Gerar QR Code com frame decorativo
     */
    public function generateWithFrame(string $content, array $design, string $format = 'svg'): ResultInterface
    {
        $qrCode = $this->generateWithDesign($content, $design, $format);
        
        // Se for SVG, adicionar frame decorativo
        if ($format === 'svg' && isset($design['frame'])) {
            $frame = $design['frame'];
            $svgContent = $qrCode->getString();
            
            // Adicionar frame SVG
            $frameSvg = $this->generateFrameSvg($frame);
            $svgContent = $this->wrapWithFrame($svgContent, $frameSvg);
            
            // Criar novo resultado com frame
            return new class($svgContent) implements ResultInterface {
                private string $content;
                
                public function __construct(string $content) {
                    $this->content = $content;
                }
                
                public function getString(): string {
                    return $this->content;
                }
                
                public function getMimeType(): string {
                    return 'image/svg+xml';
                }
            };
        }
        
        return $qrCode;
    }

    /**
     * Converter hex para Color
     */
    private function hexToColor(string $hex): Color
    {
        $hex = ltrim($hex, '#');
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        return new Color($r, $g, $b);
    }

    /**
     * Gerar frame SVG decorativo
     */
    private function generateFrameSvg(array $frame): string
    {
        $width = $frame['width'] ?? 400;
        $height = $frame['height'] ?? 400;
        $borderRadius = $frame['border_radius'] ?? 20;
        $borderWidth = $frame['border_width'] ?? 4;
        $borderColor = $frame['border_color'] ?? '#000000';
        $backgroundColor = $frame['background_color'] ?? '#ffffff';
        
        return sprintf(
            '<rect x="0" y="0" width="%d" height="%d" rx="%d" ry="%d" fill="%s" stroke="%s" stroke-width="%d"/>',
            $width,
            $height,
            $borderRadius,
            $borderRadius,
            $backgroundColor,
            $borderColor,
            $borderWidth
        );
    }

    /**
     * Envolver QR Code com frame
     */
    private function wrapWithFrame(string $qrSvg, string $frameSvg): string
    {
        // Extrair o conteúdo do QR Code SVG
        $qrContent = $this->extractSvgContent($qrSvg);
        
        // Criar SVG com frame
        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="400" height="400">%s%s</svg>',
            $frameSvg,
            $qrContent
        );
    }

    /**
     * Extrair conteúdo do SVG
     */
    private function extractSvgContent(string $svg): string
    {
        // Remover tags SVG e extrair apenas o conteúdo
        $content = preg_replace('/<svg[^>]*>/', '', $svg);
        $content = preg_replace('/<\/svg>/', '', $content);
        
        return $content;
    }

    /**
     * Salvar QR Code personalizado
     */
    public function saveCustomQrCode(string $content, array $design, string $filename, string $format = 'svg'): string
    {
        $result = $this->generateWithFrame($content, $design, $format);
        
        $filePath = 'qrcodes/custom/' . $filename . '.' . $format;
        Storage::disk('public')->put($filePath, $result->getString());
        
        return $filePath;
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
     * Obter templates de design pré-definidos
     */
    public function getDesignTemplates(): array
    {
        return [
            'classic' => [
                'name' => 'Clássico',
                'colors' => [
                    'body' => '#000000',
                    'background' => '#ffffff',
                ],
                'size' => null, // Usar resolução otimizada automaticamente
                'margin' => 20,
            ],
            'modern' => [
                'name' => 'Moderno',
                'colors' => [
                    'body' => '#3b82f6',
                    'background' => '#f8fafc',
                ],
                'size' => null, // Usar resolução otimizada automaticamente
                'margin' => 20,
                'shape' => 'rounded',
            ],
            'dark' => [
                'name' => 'Escuro',
                'colors' => [
                    'body' => '#ffffff',
                    'background' => '#1f2937',
                ],
                'size' => null, // Usar resolução otimizada automaticamente
                'margin' => 20,
            ],
            'colorful' => [
                'name' => 'Colorido',
                'colors' => [
                    'body' => '#8b5cf6',
                    'background' => '#fef3c7',
                ],
                'size' => null, // Usar resolução otimizada automaticamente
                'margin' => 20,
                'shape' => 'rounded',
            ],
            'minimal' => [
                'name' => 'Minimalista',
                'colors' => [
                    'body' => '#6b7280',
                    'background' => '#ffffff',
                ],
                'size' => null, // Usar resolução otimizada automaticamente
                'margin' => 20,
                'shape' => 'rounded',
            ],
        ];
    }
}
