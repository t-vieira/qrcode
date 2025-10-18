<?php

namespace App\Services;

use App\Models\QrCode;
use App\Services\QrTypes\QrTypeInterface;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\EpsWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeGeneratorService
{
    protected BuilderInterface $builder;

    public function __construct()
    {
        // Configurações otimizadas para servidor compartilhado
        $maxResolution = config('qrcode.max_resolution', 1500);
        $defaultResolution = min(300, $maxResolution);
        
        $this->builder = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data('')
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size($defaultResolution)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin());
    }

    public function generate(QrCode $qrCode): string
    {
        $content = $this->getContentForQrCode($qrCode);
        $design = $qrCode->design ?? [];
        
        $this->builder->data($content);
        
        // Aplicar customizações de design
        $this->applyDesign($design);
        
        // Gerar o QR Code
        $result = $this->builder->build();
        
        // Salvar o arquivo
        $filePath = $this->saveQrCode($qrCode, $result);
        
        return $filePath;
    }

    protected function getContentForQrCode(QrCode $qrCode): string
    {
        $typeClass = "App\\Services\\QrTypes\\" . ucfirst($qrCode->type) . "QrType";
        
        if (class_exists($typeClass)) {
            $typeInstance = new $typeClass;
            if ($typeInstance instanceof QrTypeInterface) {
                return $typeInstance->generateContent($qrCode->content);
            }
        }

        // Fallback para tipos básicos
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

    protected function applyDesign(array $design): void
    {
        // Cores
        $foregroundColor = $design['foregroundColor'] ?? '#000000';
        $backgroundColor = $design['backgroundColor'] ?? '#ffffff';
        $eyeColor = $design['eyeColor'] ?? $foregroundColor;

        $this->builder
            ->foregroundColor(new Color($foregroundColor))
            ->backgroundColor(new Color($backgroundColor));

        // Tamanho (limitado para servidor compartilhado)
        if (isset($design['resolution'])) {
            $maxResolution = config('qrcode.max_resolution', 1500);
            $resolution = min($design['resolution'], $maxResolution);
            $this->builder->size($resolution);
        }

        // Logo
        if (!empty($design['logo']) && Storage::exists($design['logo'])) {
            $logoPath = Storage::path($design['logo']);
            $logo = Logo::create($logoPath)
                ->setResizeToWidth(60)
                ->setResizeToHeight(60);
            $this->builder->logo($logo);
        }

        // Label/Sticker
        if (!empty($design['sticker'])) {
            $label = Label::create($design['sticker'])
                ->setTextColor(new Color('#000000'));
            $this->builder->label($label);
        }
    }

    protected function saveQrCode(QrCode $qrCode, ResultInterface $result): string
    {
        $format = $qrCode->format ?? 'png';
        $fileName = Str::slug($qrCode->name) . '_' . $qrCode->id . '.' . $format;
        $directory = "qrcodes/{$qrCode->user_id}";
        
        // Criar diretório se não existir
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        
        $filePath = "{$directory}/{$fileName}";
        
        // Salvar o arquivo
        Storage::put($filePath, $result->getString());
        
        return $filePath;
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

    public function generatePreview(string $content, array $design = []): string
    {
        $this->builder->data($content);
        $this->applyDesign($design);
        
        $result = $this->builder->build();
        
        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    public function download(QrCode $qrCode, string $format = null): string
    {
        $format = $format ?? $qrCode->format;
        $content = $this->getContentForQrCode($qrCode);
        $design = $qrCode->design ?? [];
        
        // Configurar writer baseado no formato
        $writer = match ($format) {
            'svg' => new SvgWriter(),
            'eps' => new EpsWriter(),
            default => new PngWriter(),
        };
        
        $this->builder->writer($writer);
        $this->builder->data($content);
        $this->applyDesign($design);
        
        $result = $this->builder->build();
        
        return $result->getString();
    }
}
