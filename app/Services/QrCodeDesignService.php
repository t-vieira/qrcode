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
            return new class ($svgContent) implements ResultInterface {
                private string $content;

                public function __construct(string $content)
                {
                    $this->content = $content;
                }

                public function getString(): string
                {
                    return $this->content;
                }

                public function getMimeType(): string
                {
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
    /**
     * Gerar frame SVG decorativo
     */
    private function generateFrameSvg(array $frame): string
    {
        $style = $frame['style'] ?? 'simple';
        $label = $frame['label'] ?? 'SCAN ME';
        $color = $frame['color'] ?? '#000000';
        $textColor = $frame['text_color'] ?? '#ffffff';

        switch ($style) {
            case 'bubble_bottom':
                return $this->generateBubbleBottomFrame($label, $color, $textColor);
            case 'bubble_top':
                return $this->generateBubbleTopFrame($label, $color, $textColor);
            case 'polaroid':
                return $this->generatePolaroidFrame($label, $color, $textColor);
            case 'phone':
                return $this->generatePhoneFrame($label, $color, $textColor);
            case 'simple':
            default:
                $width = $frame['width'] ?? 400;
                $height = $frame['height'] ?? 400;
                $borderRadius = $frame['border_radius'] ?? 20;
                $borderWidth = $frame['border_width'] ?? 4;
                $borderColor = $frame['border_color'] ?? $color;
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
    }

    private function generateBubbleBottomFrame(string $label, string $color, string $textColor): string
    {
        // Frame estilo balão embaixo
        return sprintf(
            '<g>
                <rect x="10" y="10" width="380" height="380" rx="20" ry="20" fill="none" stroke="%s" stroke-width="10"/>
                <path d="M100 390 L130 420 L160 390 Z" fill="%s"/>
                <rect x="60" y="410" width="280" height="60" rx="30" ry="30" fill="%s"/>
                <text x="200" y="450" font-family="Arial, sans-serif" font-size="30" font-weight="bold" fill="%s" text-anchor="middle">%s</text>
            </g>',
            $color,
            $color,
            $color,
            $textColor,
            $label
        );
    }

    private function generateBubbleTopFrame(string $label, string $color, string $textColor): string
    {
        // Frame estilo balão em cima
        return sprintf(
            '<g>
                <rect x="60" y="30" width="280" height="60" rx="30" ry="30" fill="%s"/>
                <path d="M100 90 L130 120 L160 90 Z" fill="%s"/>
                <text x="200" y="70" font-family="Arial, sans-serif" font-size="30" font-weight="bold" fill="%s" text-anchor="middle">%s</text>
                <rect x="10" y="110" width="380" height="380" rx="20" ry="20" fill="none" stroke="%s" stroke-width="10"/>
            </g>',
            $color,
            $color,
            $textColor,
            $label,
            $color
        );
    }

    private function generatePolaroidFrame(string $label, string $color, string $textColor): string
    {
        // Frame estilo polaroid
        return sprintf(
            '<g>
                <rect x="0" y="0" width="400" height="500" rx="10" ry="10" fill="#ffffff" stroke="#e5e7eb" stroke-width="2"/>
                <rect x="25" y="25" width="350" height="350" fill="none" stroke="#e5e7eb" stroke-width="1"/>
                <text x="200" y="450" font-family="Arial, sans-serif" font-size="30" font-weight="bold" fill="%s" text-anchor="middle">%s</text>
            </g>',
            $color,
            $label
        );
    }

    private function generatePhoneFrame(string $label, string $color, string $textColor): string
    {
        // Frame estilo celular
        return sprintf(
            '<g>
                <rect x="20" y="0" width="360" height="600" rx="40" ry="40" fill="#ffffff" stroke="%s" stroke-width="15"/>
                <rect x="150" y="20" width="100" height="10" rx="5" ry="5" fill="%s"/>
                <circle cx="200" cy="550" r="25" fill="none" stroke="%s" stroke-width="5"/>
                <text x="200" y="500" font-family="Arial, sans-serif" font-size="24" font-weight="bold" fill="%s" text-anchor="middle">%s</text>
            </g>',
            $color,
            $color,
            $color,
            $color,
            $label
        );
    }

    /**
     * Envolver QR Code com frame
     */
    private function wrapWithFrame(string $qrSvg, string $frameSvg): string
    {
        // Extrair o conteúdo do QR Code SVG
        $qrContent = $this->extractSvgContent($qrSvg);

        // Ajustar viewBox baseado no estilo do frame
        $viewBox = '0 0 400 400';
        $width = 400;
        $height = 400;

        // Se o frame SVG contém dimensões específicas, tentar extrair ou usar defaults por tipo
        if (strpos($frameSvg, 'height="500"') !== false) { // Polaroid
            $viewBox = '0 0 400 500';
            $height = 500;
            // Ajustar posição do QR code para polaroid
            $qrContent = preg_replace('/x="[^"]*"/', 'x="25"', $qrContent, 1);
            $qrContent = preg_replace('/y="[^"]*"/', 'y="25"', $qrContent, 1);
            // Redimensionar QR code para caber na polaroid (350x350)
            $qrContent = str_replace('width="400"', 'width="350"', $qrContent);
            $qrContent = str_replace('height="400"', 'height="350"', $qrContent);
        } elseif (strpos($frameSvg, 'height="600"') !== false) { // Phone
            $viewBox = '0 0 400 600';
            $height = 600;
            // Ajustar posição do QR code para phone
            $qrContent = preg_replace('/x="[^"]*"/', 'x="50"', $qrContent, 1);
            $qrContent = preg_replace('/y="[^"]*"/', 'y="80"', $qrContent, 1);
            // Redimensionar QR code para caber no phone (300x300)
            $qrContent = str_replace('width="400"', 'width="300"', $qrContent);
            $qrContent = str_replace('height="400"', 'height="300"', $qrContent);
        } elseif (strpos($frameSvg, 'y="110"') !== false) { // Bubble Top
            $viewBox = '0 0 400 500';
            $height = 500;
            // Ajustar posição do QR code para bubble top
            $qrContent = preg_replace('/x="[^"]*"/', 'x="20"', $qrContent, 1);
            $qrContent = preg_replace('/y="[^"]*"/', 'y="120"', $qrContent, 1);
            // Redimensionar QR code
            $qrContent = str_replace('width="400"', 'width="360"', $qrContent);
            $qrContent = str_replace('height="400"', 'height="360"', $qrContent);
        } elseif (strpos($frameSvg, 'y="410"') !== false) { // Bubble Bottom
            $viewBox = '0 0 400 500';
            $height = 500;
            // Ajustar posição do QR code para bubble bottom
            $qrContent = preg_replace('/x="[^"]*"/', 'x="20"', $qrContent, 1);
            $qrContent = preg_replace('/y="[^"]*"/', 'y="20"', $qrContent, 1);
            // Redimensionar QR code
            $qrContent = str_replace('width="400"', 'width="360"', $qrContent);
            $qrContent = str_replace('height="400"', 'height="360"', $qrContent);
        }

        // Criar SVG com frame
        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="%s" width="%d" height="%d">%s%s</svg>',
            $viewBox,
            $width,
            $height,
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
