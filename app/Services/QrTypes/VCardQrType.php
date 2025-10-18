<?php

namespace App\Services\QrTypes;

class VCardQrType implements QrTypeInterface
{
    public function generateContent(array $data): string
    {
        $vcard = "BEGIN:VCARD\n";
        $vcard .= "VERSION:3.0\n";
        
        if (!empty($data['firstName']) || !empty($data['lastName'])) {
            $vcard .= "FN:" . trim(($data['firstName'] ?? '') . ' ' . ($data['lastName'] ?? '')) . "\n";
            $vcard .= "N:" . ($data['lastName'] ?? '') . ";" . ($data['firstName'] ?? '') . ";;;\n";
        }
        
        if (!empty($data['organization'])) {
            $vcard .= "ORG:" . $data['organization'] . "\n";
        }
        
        if (!empty($data['title'])) {
            $vcard .= "TITLE:" . $data['title'] . "\n";
        }
        
        if (!empty($data['phone'])) {
            $vcard .= "TEL:" . $data['phone'] . "\n";
        }
        
        if (!empty($data['email'])) {
            $vcard .= "EMAIL:" . $data['email'] . "\n";
        }
        
        if (!empty($data['website'])) {
            $website = $data['website'];
            if (!preg_match('/^https?:\/\//', $website)) {
                $website = 'https://' . $website;
            }
            $vcard .= "URL:" . $website . "\n";
        }
        
        $vcard .= "END:VCARD";
        
        return $vcard;
    }

    public function getValidationRules(): array
    {
        return [
            'firstName' => 'nullable|string|max:100',
            'lastName' => 'nullable|string|max:100',
            'organization' => 'nullable|string|max:200',
            'title' => 'nullable|string|max:200',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:500',
        ];
    }

    public function getFormFields(): array
    {
        return [
            [
                'name' => 'firstName',
                'type' => 'text',
                'label' => 'Nome',
                'placeholder' => 'João',
            ],
            [
                'name' => 'lastName',
                'type' => 'text',
                'label' => 'Sobrenome',
                'placeholder' => 'Silva',
            ],
            [
                'name' => 'organization',
                'type' => 'text',
                'label' => 'Empresa',
                'placeholder' => 'Minha Empresa Ltda',
            ],
            [
                'name' => 'title',
                'type' => 'text',
                'label' => 'Cargo',
                'placeholder' => 'Gerente de Vendas',
            ],
            [
                'name' => 'phone',
                'type' => 'tel',
                'label' => 'Telefone',
                'placeholder' => '(11) 99999-9999',
            ],
            [
                'name' => 'email',
                'type' => 'email',
                'label' => 'E-mail',
                'placeholder' => 'joao@empresa.com',
            ],
            [
                'name' => 'website',
                'type' => 'url',
                'label' => 'Website',
                'placeholder' => 'https://empresa.com',
            ],
        ];
    }
}
