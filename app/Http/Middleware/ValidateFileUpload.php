<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class ValidateFileUpload
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se há uploads de arquivo
        if ($request->hasFile('logo') || $request->hasFile('sticker') || $request->hasFile('image')) {
            $this->validateFileUploads($request);
        }

        return $next($request);
    }

    /**
     * Validar uploads de arquivo
     */
    private function validateFileUploads(Request $request): void
    {
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml',
        ];

        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $maxDimensions = 2000; // 2000x2000 pixels

        $files = ['logo', 'sticker', 'image'];

        foreach ($files as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);

                // Verificar se é um arquivo válido
                if (!$file->isValid()) {
                    Log::warning('Invalid file upload attempt', [
                        'file' => $fileKey,
                        'error' => $file->getError(),
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    abort(400, 'Arquivo inválido enviado.');
                }

                // Verificar tipo MIME
                if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                    Log::warning('Invalid file type upload attempt', [
                        'file' => $fileKey,
                        'mime_type' => $file->getMimeType(),
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    abort(400, 'Tipo de arquivo não permitido.');
                }

                // Verificar tamanho do arquivo
                if ($file->getSize() > $maxFileSize) {
                    Log::warning('File too large upload attempt', [
                        'file' => $fileKey,
                        'size' => $file->getSize(),
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                    abort(400, 'Arquivo muito grande. Tamanho máximo: 5MB.');
                }

                // Verificar dimensões da imagem (apenas para imagens)
                if (in_array($file->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'])) {
                    $this->validateImageDimensions($file, $maxDimensions, $fileKey, $request);
                }

                // Verificar se o arquivo não é malicioso
                $this->scanFileForMalware($file, $fileKey, $request);
            }
        }
    }

    /**
     * Validar dimensões da imagem
     */
    private function validateImageDimensions($file, int $maxDimensions, string $fileKey, Request $request): void
    {
        try {
            $imageInfo = getimagesize($file->getPathname());
            
            if ($imageInfo === false) {
                Log::warning('Invalid image file upload attempt', [
                    'file' => $fileKey,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                abort(400, 'Arquivo de imagem inválido.');
            }

            $width = $imageInfo[0];
            $height = $imageInfo[1];

            if ($width > $maxDimensions || $height > $maxDimensions) {
                Log::warning('Image dimensions too large upload attempt', [
                    'file' => $fileKey,
                    'width' => $width,
                    'height' => $height,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                abort(400, "Dimensões da imagem muito grandes. Máximo: {$maxDimensions}x{$maxDimensions} pixels.");
            }

            // Verificar se a imagem não é muito pequena
            if ($width < 10 || $height < 10) {
                Log::warning('Image dimensions too small upload attempt', [
                    'file' => $fileKey,
                    'width' => $width,
                    'height' => $height,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                abort(400, 'Dimensões da imagem muito pequenas.');
            }

        } catch (\Exception $e) {
            Log::error('Error validating image dimensions', [
                'file' => $fileKey,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            abort(400, 'Erro ao validar imagem.');
        }
    }

    /**
     * Verificar se o arquivo não é malicioso
     */
    private function scanFileForMalware($file, string $fileKey, Request $request): void
    {
        try {
            $content = file_get_contents($file->getPathname());
            
            // Verificar assinaturas de arquivos suspeitos
            $suspiciousSignatures = [
                '<?php',
                '<script',
                'javascript:',
                'vbscript:',
                'onload=',
                'onerror=',
                'eval(',
                'exec(',
                'system(',
                'shell_exec(',
                'passthru(',
                'file_get_contents(',
                'fopen(',
                'fwrite(',
            ];

            foreach ($suspiciousSignatures as $signature) {
                if (stripos($content, $signature) !== false) {
                    Log::critical('Malicious file upload attempt detected', [
                        'file' => $fileKey,
                        'signature' => $signature,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'user_id' => auth()->id(),
                    ]);
                    abort(400, 'Arquivo suspeito detectado.');
                }
            }

            // Verificar se é um arquivo SVG e validar seu conteúdo
            if ($file->getMimeType() === 'image/svg+xml') {
                $this->validateSvgContent($content, $fileKey, $request);
            }

        } catch (\Exception $e) {
            Log::error('Error scanning file for malware', [
                'file' => $fileKey,
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            abort(400, 'Erro ao verificar arquivo.');
        }
    }

    /**
     * Validar conteúdo SVG
     */
    private function validateSvgContent(string $content, string $fileKey, Request $request): void
    {
        // Verificar se contém elementos perigosos
        $dangerousElements = [
            '<script',
            '<iframe',
            '<object',
            '<embed',
            '<link',
            '<meta',
            'onload',
            'onerror',
            'onclick',
            'onmouseover',
            'javascript:',
            'vbscript:',
        ];

        foreach ($dangerousElements as $element) {
            if (stripos($content, $element) !== false) {
                Log::critical('Malicious SVG upload attempt detected', [
                    'file' => $fileKey,
                    'element' => $element,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'user_id' => auth()->id(),
                ]);
                abort(400, 'SVG contém elementos perigosos.');
            }
        }

        // Verificar se é um SVG válido
        if (!preg_match('/<svg[^>]*>/i', $content)) {
            Log::warning('Invalid SVG file upload attempt', [
                'file' => $fileKey,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            abort(400, 'Arquivo SVG inválido.');
        }
    }
}