<?php

namespace App\Services\QrTypes;

class UrlQrType implements QrTypeInterface
{
    public function generateContent(array $data): string
    {
        $url = $data['url'] ?? '';
        
        // Adicionar protocolo se não tiver
        if (!empty($url) && !preg_match('/^https?:\/\//', $url)) {
            $url = 'https://' . $url;
        }
        
        return $url;
    }

    public function getValidationRules(): array
    {
        return [
            'url' => 'required|url|max:2048',
        ];
    }

    public function getFormFields(): array
    {
        return [
            [
                'name' => 'url',
                'type' => 'url',
                'label' => 'URL do Site',
                'placeholder' => 'https://exemplo.com',
                'required' => true,
            ],
        ];
    }
}
