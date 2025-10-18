<?php

namespace App\Services\QrTypes;

interface QrTypeInterface
{
    /**
     * Generate the content string for the QR Code
     */
    public function generateContent(array $data): string;

    /**
     * Get the validation rules for this QR type
     */
    public function getValidationRules(): array;

    /**
     * Get the form fields configuration for this QR type
     */
    public function getFormFields(): array;
}
