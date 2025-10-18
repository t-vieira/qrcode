<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Folder;
use App\Services\QrCodeGeneratorService;
use App\Services\ShortUrlService;
use App\Services\CacheService;
use App\Services\QueueService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class QrCodeController extends Controller
{
    protected QrCodeGeneratorService $qrGenerator;
    protected ShortUrlService $shortUrlService;
    protected CacheService $cacheService;
    protected QueueService $queueService;

    public function __construct(
        QrCodeGeneratorService $qrGenerator,
        ShortUrlService $shortUrlService,
        CacheService $cacheService,
        QueueService $queueService
    ) {
        $this->qrGenerator = $qrGenerator;
        $this->shortUrlService = $shortUrlService;
        $this->cacheService = $cacheService;
        $this->queueService = $queueService;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = $user->qrCodes()->with(['folder', 'scans']);
        
        // Filtros
        if ($request->filled('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_code', 'like', "%{$search}%");
            });
        }
        
        $qrCodes = $query->latest()->paginate(20);
        $folders = $user->folders()->orderBy('name')->get();
        
        return view('qrcodes.index', compact('qrCodes', 'folders'));
    }

    public function create()
    {
        $qrTypes = $this->getQrTypes();
        $folders = auth()->user()->folders()->orderBy('name')->get();
        
        return view('qrcodes.create', compact('qrTypes', 'folders'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys($this->getQrTypes())),
            'folder_id' => 'nullable|exists:folders,id',
            'short_code' => 'nullable|string|max:20|unique:qr_codes,short_code',
            'is_dynamic' => 'boolean',
            'content' => 'required|array',
            'design' => 'nullable|array',
            'resolution' => 'integer|min:100|max:2000',
            'format' => 'string|in:png,jpg,svg,eps',
        ]);
        
        // Validar conteúdo baseado no tipo
        $this->validateContent($validated['type'], $validated['content']);
        
        // Gerar short_code se não fornecido
        if (empty($validated['short_code'])) {
            $validated['short_code'] = $this->shortUrlService->generateShortCode(new QrCode());
        } else {
            // Validar código customizado
            $errors = $this->shortUrlService->validateCustomCode($validated['short_code']);
            if (!empty($errors)) {
                return back()->withErrors(['short_code' => $errors[0]])->withInput();
            }
        }
        
        // Verificar se pode criar QR dinâmico
        if ($validated['is_dynamic'] && !$user->canAccessAdvancedFeatures()) {
            $validated['is_dynamic'] = false;
        }
        
        $qrCode = $user->qrCodes()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'folder_id' => $validated['folder_id'] ?? null,
            'short_code' => $validated['short_code'],
            'is_dynamic' => $validated['is_dynamic'] ?? false,
            'content' => $validated['content'],
            'design' => $validated['design'] ?? [],
            'resolution' => $validated['resolution'] ?? 300,
            'format' => $validated['format'] ?? 'png',
        ]);
        
        // Gerar QR Code em fila para melhor performance
        $this->queueService->generateQrCodeFile($qrCode, [
            'content' => $validated['content'],
            'design' => $validated['design'] ?? [],
            'format' => $validated['format'] ?? 'png',
            'resolution' => $validated['resolution'] ?? 300,
        ]);

        // Invalidar cache do usuário
        $this->cacheService->invalidateUserStats($user);
        
        return redirect()->route('qrcodes.show', $qrCode)
            ->with('success', 'QR Code criado com sucesso! O arquivo está sendo gerado em segundo plano.');
    }

    public function show(QrCode $qrCode)
    {
        $this->authorize('view', $qrCode);
        
        // Usar cache para estatísticas do QR Code
        $stats = $this->cacheService->getQrCodeStats($qrCode);
        
        $qrCode->load(['folder', 'scans' => function ($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('qrcodes.show', compact('qrCode', 'stats'));
    }

    public function edit(QrCode $qrCode)
    {
        $this->authorize('update', $qrCode);
        
        $qrTypes = $this->getQrTypes();
        $folders = auth()->user()->folders()->orderBy('name')->get();
        
        return view('qrcodes.edit', compact('qrCode', 'qrTypes', 'folders'));
    }

    public function update(Request $request, QrCode $qrCode)
    {
        $this->authorize('update', $qrCode);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'folder_id' => 'nullable|exists:folders,id',
            'short_code' => 'required|string|max:20|unique:qr_codes,short_code,' . $qrCode->id,
            'content' => 'required|array',
            'design' => 'nullable|array',
            'resolution' => 'integer|min:100|max:2000',
            'format' => 'string|in:png,jpg,svg,eps',
        ]);
        
        // Validar conteúdo baseado no tipo
        $this->validateContent($qrCode->type, $validated['content']);
        
        // Validar código customizado
        if ($validated['short_code'] !== $qrCode->short_code) {
            $errors = $this->shortUrlService->validateCustomCode($validated['short_code']);
            if (!empty($errors)) {
                return back()->withErrors(['short_code' => $errors[0]])->withInput();
            }
        }
        
        $qrCode->update($validated);
        
        // Regenerar QR Code se necessário
        if ($this->needsRegeneration($qrCode, $validated)) {
            $filePath = $this->qrGenerator->generate($qrCode);
            $qrCode->update(['file_path' => $filePath]);
        }
        
        return redirect()->route('qrcodes.show', $qrCode)
            ->with('success', 'QR Code atualizado com sucesso!');
    }

    public function destroy(QrCode $qrCode)
    {
        $this->authorize('delete', $qrCode);
        
        // Deletar arquivo físico
        if ($qrCode->file_path && Storage::exists($qrCode->file_path)) {
            Storage::delete($qrCode->file_path);
        }
        
        $qrCode->delete();
        
        return redirect()->route('qrcodes.index')
            ->with('success', 'QR Code excluído com sucesso!');
    }

    public function download(QrCode $qrCode, string $format = null)
    {
        $this->authorize('view', $qrCode);
        
        $format = $format ?? $qrCode->format;
        $content = $this->qrGenerator->download($qrCode, $format);
        $fileName = Str::slug($qrCode->name) . '.' . $format;
        
        return response($content)
            ->header('Content-Type', $this->getMimeType($format))
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'content' => 'required|array',
            'design' => 'nullable|array',
        ]);
        
        $content = $this->getContentForPreview($validated['content']);
        $preview = $this->qrGenerator->generatePreview($content, $validated['design'] ?? []);
        
        return response()->json(['url' => $preview]);
    }

    protected function getQrTypes(): array
    {
        return [
            'url' => 'URL (Site)',
            'vcard' => 'vCard (Contato)',
            'business' => 'Página de Negócio',
            'coupon' => 'Cupom/Desconto',
            'text' => 'Texto Livre',
            'mp3' => 'MP3/Áudio',
            'pdf' => 'PDF/Documento',
            'image' => 'Imagem',
            'video' => 'Vídeo',
            'app' => 'App (Google Play, App Store)',
            'menu' => 'Menu Digital',
            'email' => 'E-mail',
            'phone' => 'Telefone',
            'sms' => 'SMS',
            'social' => 'Redes Sociais',
            'wifi' => 'Wi-Fi',
            'event' => 'Evento',
            'location' => 'Localização',
            'feedback' => 'Feedback e Avaliação',
            'crypto' => 'Carteira de Criptomoedas',
        ];
    }

    protected function validateContent(string $type, array $content): void
    {
        $typeClass = "App\\Services\\QrTypes\\" . ucfirst($type) . "QrType";
        
        if (class_exists($typeClass)) {
            $typeInstance = new $typeClass;
            if ($typeInstance instanceof \App\Services\QrTypes\QrTypeInterface) {
                $rules = $typeInstance->getValidationRules();
                
                $validator = validator($content, $rules);
                if ($validator->fails()) {
                    throw new \Illuminate\Validation\ValidationException($validator);
                }
            }
        }
    }

    protected function needsRegeneration(QrCode $qrCode, array $validated): bool
    {
        return $qrCode->content !== $validated['content'] ||
               $qrCode->design !== $validated['design'] ||
               $qrCode->resolution !== $validated['resolution'] ||
               $qrCode->format !== $validated['format'];
    }

    protected function getMimeType(string $format): string
    {
        return match ($format) {
            'jpg', 'jpeg' => 'image/jpeg',
            'svg' => 'image/svg+xml',
            'eps' => 'application/postscript',
            default => 'image/png',
        };
    }

    protected function getContentForPreview(array $content): string
    {
        // Implementar lógica básica para preview
        return $content['url'] ?? $content['text'] ?? 'Preview do QR Code';
    }
}
