<?php

namespace App\Services;

use App\Models\QrCode;
use Illuminate\Support\Str;

class ShortUrlService
{
    protected int $maxAttempts = 10;
    protected int $codeLength = 6;

    public function generateShortCode(QrCode $qrCode): string
    {
        $attempts = 0;
        
        do {
            $shortCode = $this->generateRandomCode();
            $attempts++;
            
            if ($attempts >= $this->maxAttempts) {
                // Se não conseguir gerar um código único, usar timestamp
                $shortCode = $this->generateTimestampCode();
                break;
            }
        } while ($this->isCodeInUse($shortCode));
        
        return $shortCode;
    }

    public function generateCustomShortCode(string $customCode): string
    {
        // Limpar e validar o código customizado
        $customCode = $this->sanitizeCode($customCode);
        
        if ($this->isCodeInUse($customCode)) {
            throw new \Exception('Este código já está em uso. Escolha outro.');
        }
        
        return $customCode;
    }

    protected function generateRandomCode(): string
    {
        // Usar caracteres alfanuméricos (sem 0, O, I, l para evitar confusão)
        $characters = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        
        return Str::random($this->codeLength, $characters);
    }

    protected function generateTimestampCode(): string
    {
        // Usar timestamp base36 para garantir unicidade
        return base_convert(time(), 10, 36);
    }

    protected function sanitizeCode(string $code): string
    {
        // Remover caracteres especiais e converter para minúsculo
        $code = strtolower(trim($code));
        $code = preg_replace('/[^a-z0-9\-_]/', '', $code);
        
        // Limitar tamanho
        $code = substr($code, 0, 20);
        
        if (empty($code)) {
            throw new \Exception('Código inválido.');
        }
        
        return $code;
    }

    protected function isCodeInUse(string $code): bool
    {
        return QrCode::where('short_code', $code)->exists();
    }

    public function getUrl(QrCode $qrCode): string
    {
        $domain = $qrCode->custom_domain ?: config('app.url');
        return rtrim($domain, '/') . '/' . $qrCode->short_code;
    }

    public function validateCustomCode(string $code): array
    {
        $errors = [];
        
        if (empty($code)) {
            $errors[] = 'Código não pode estar vazio.';
        }
        
        if (strlen($code) < 3) {
            $errors[] = 'Código deve ter pelo menos 3 caracteres.';
        }
        
        if (strlen($code) > 20) {
            $errors[] = 'Código deve ter no máximo 20 caracteres.';
        }
        
        if (!preg_match('/^[a-z0-9\-_]+$/', $code)) {
            $errors[] = 'Código pode conter apenas letras minúsculas, números, hífens e underscores.';
        }
        
        // Verificar palavras reservadas
        $reservedWords = [
            'admin', 'api', 'app', 'www', 'mail', 'ftp', 'blog', 'shop',
            'help', 'support', 'contact', 'about', 'terms', 'privacy',
            'login', 'register', 'dashboard', 'profile', 'settings',
            'billing', 'subscription', 'upgrade', 'download', 'upload',
        ];
        
        if (in_array(strtolower($code), $reservedWords)) {
            $errors[] = 'Este código é reservado e não pode ser usado.';
        }
        
        if ($this->isCodeInUse($code)) {
            $errors[] = 'Este código já está em uso.';
        }
        
        return $errors;
    }
}
