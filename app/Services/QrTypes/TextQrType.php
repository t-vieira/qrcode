<?php

namespace App\Services\QrTypes;

class TextQrType implements QrTypeInterface
{
    public function generateContent(array $data): string
    {
        return $data['text'] ?? '';
    }

    public function getValidationRules(): array
    {
        return [
            'text' => 'required|string|max:2953', // Limite do QR Code
        ];
    }

    public function getFormFields(): array
    {
        return [
            [
                'name' => 'text',
                'type' => 'textarea',
                'label' => 'Texto',
                'placeholder' => 'Digite o texto que aparecerá no QR Code...',
                'required' => true,
                'rows' => 5,
            ],
        ];
    }
}
