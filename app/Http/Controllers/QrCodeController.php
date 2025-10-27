<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\Folder;
use App\Services\QrCodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class QrCodeController extends Controller
{
    use AuthorizesRequests;
    
    protected QrCodeGeneratorService $qrGenerator;

    public function __construct(QrCodeGeneratorService $qrGenerator)
    {
        $this->qrGenerator = $qrGenerator;
    }
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Query base
        $query = $user->qrCodes();
        
        // Filtro por status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filtro por tipo
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }
        
        // Busca por nome
        if ($request->has('search') && !empty($request->search)) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Ordenação
        $orderBy = $request->get('order', 'created_at');
        $orderDirection = $request->get('direction', 'desc');
        $query->orderBy($orderBy, $orderDirection);
        
        // Buscar QR Codes do usuário com pasta
        $qrCodes = $query->with('folder')->paginate(20);
        
        // Estatísticas básicas
        $stats = [
            'total' => $user->qrCodes()->count(),
            'active' => $user->qrCodes()->where('status', 'active')->count(),
            'archived' => $user->qrCodes()->where('status', 'archived')->count(),
            'total_scans' => 0, // Por enquanto
        ];
        
        return view('qrcodes.index', compact('qrCodes', 'stats'));
    }

    public function create(Request $request)
    {
        \Log::info('QR Code create page accessed', [
            'user_id' => auth()->id(),
            'user_email' => auth()->user() ? auth()->user()->email : 'not_authenticated',
            'is_authenticated' => auth()->check(),
            'request_method' => request()->method(),
            'request_url' => request()->url(),
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip()
        ]);

        // Buscar pastas do usuário
        $folders = auth()->user()->folders()
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        // Pasta selecionada via parâmetro
        $selectedFolderId = $request->get('folder');

        return view('qrcodes.create', compact('folders', 'selectedFolderId'));
    }

    public function store(Request $request)
    {
        try {
            // Log para debug
            \Log::info('QR Code store called', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user() ? auth()->user()->email : 'not_authenticated',
                'is_authenticated' => auth()->check(),
                'request_method' => request()->method(),
                'request_url' => request()->url(),
                'request_data' => $request->all(),
                'csrf_token' => $request->input('_token'),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip()
            ]);

            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|string|in:url,vcard,text,email,phone,sms,wifi,location',
                'content' => 'required|string',
                'design' => 'nullable|string', // Aceita string JSON
                'folder_id' => 'nullable|exists:folders,id',
            ]);

            $user = $request->user();
        
        // Gerar código curto único
        $shortCode = $this->generateUniqueShortCode();
        
        // Gerar nome único para o arquivo
        $filename = $this->qrGenerator->generateUniqueFilename();
        
        // Gerar URL curta para redirecionamento
        $shortUrl = url('/r/' . $shortCode);
        
        // Preparar dados de design
        $design = $request->input('design', []);
        
        // Se design veio como string JSON, decodificar
        if (is_string($design)) {
            $design = json_decode($design, true) ?: [];
        }
        
        if (empty($design)) {
            // Design padrão se não especificado
            $design = [
                'colors' => [
                    'body' => '#000000',
                    'background' => '#ffffff'
                ],
                'size' => 300,
                'margin' => 10,
                'shape' => 'square'
            ];
        }
        
        // Gerar e salvar o QR Code com design personalizado
        $filePath = $this->qrGenerator->generateAndSave($shortUrl, $filename, 'svg', $design);
        
        $qrCode = $user->qrCodes()->create([
            'name' => $request->name,
            'type' => $request->type,
            'content' => $request->content,
            'short_code' => $shortCode,
            'file_path' => $filePath,
            'design' => $design,
            'status' => 'active',
            'is_dynamic' => false, // Por enquanto, sempre estático
            'folder_id' => $request->folder_id,
        ]);

        \Log::info('QR Code created successfully', [
            'qr_code_id' => $qrCode->id,
            'qr_code_name' => $qrCode->name,
            'user_id' => $user->id,
            'short_code' => $shortCode,
            'file_path' => $filePath
        ]);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'QR Code criado com sucesso!',
                'qr_code_id' => $qrCode->id,
                'redirect_url' => route('dashboard')
            ])->header('Content-Type', 'application/json');
        }

        return redirect()->route('qrcodes.show', $qrCode)
            ->with('success', 'QR Code criado com sucesso!');
                
        } catch (\Exception $e) {
            \Log::error('QR Code creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);
            
            // Return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar QR Code: ' . $e->getMessage()
                ], 500)->header('Content-Type', 'application/json');
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao criar QR Code: ' . $e->getMessage()]);
        }
    }

    public function show($qrcode)
    {
        // Buscar o QR code manualmente
        $qrCode = QrCode::findOrFail($qrcode);
        
        if (!$qrCode) {
            \Log::error('QR Code not found for show', ['qr_code_id' => $qrcode]);
            abort(404, 'QR Code não encontrado');
        }
        
        // Log para debug
        \Log::info('QR Code show called', [
            'qr_code_id' => $qrCode->id,
            'qr_code_name' => $qrCode->name,
            'qr_code_user_id' => $qrCode->user_id,
            'current_user_id' => auth()->id(),
            'current_user_email' => auth()->user() ? auth()->user()->email : 'not_authenticated',
            'is_admin' => auth()->user() ? auth()->user()->hasRole('admin') : false,
            'can_access' => ($qrCode->user_id === auth()->id() || (auth()->user() && auth()->user()->hasRole('admin'))),
            'request_method' => request()->method(),
            'request_url' => request()->url()
        ]);

        // Verificar se o usuário pode acessar este QR Code
        // Admins podem acessar qualquer QR code, usuários normais apenas os seus
        if ($qrCode->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            \Log::warning('Access denied for QR code show', [
                'qr_code_id' => $qrCode->id,
                'qr_code_user_id' => $qrCode->user_id,
                'current_user_id' => auth()->id()
            ]);
            abort(403);
        }

        // Retornar JSON para requisições AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'short_code' => $qrCode->short_code,
                'name' => $qrCode->name,
                'type' => $qrCode->type,
                'status' => $qrCode->status,
                'url' => url('/r/' . $qrCode->short_code)
            ]);
        }

        // Carregar estatísticas de scan
        $stats = $this->getQrCodeStats($qrCode);
        
        // Carregar scans recentes
        $recentScans = $qrCode->scans()
            ->with('qrCode')
            ->latest('scanned_at')
            ->limit(10)
            ->get();

        return view('qrcodes.show', compact('qrCode', 'stats', 'recentScans'));
    }

    /**
     * Exibir todos os scans de um QR Code
     */
    public function scans($qrcode, Request $request)
    {
        $qrCode = QrCode::findOrFail($qrcode);
        
        // Verificar se o usuário pode acessar este QR Code
        // Admins podem acessar qualquer QR code, usuários normais apenas os seus
        if ($qrCode->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $query = $qrCode->scans()->latest('scanned_at');

        // Filtros
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }

        if ($request->filled('unique_only')) {
            $query->where('is_unique', true);
        }

        $scans = $query->paginate(50);

        // Estatísticas para filtros
        $deviceTypes = $qrCode->scans()
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->pluck('count', 'device_type');

        $countries = $qrCode->scans()
            ->whereNotNull('country')
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->pluck('count', 'country');

        return view('qrcodes.scans', compact('qrCode', 'scans', 'deviceTypes', 'countries'));
    }

    public function edit($qrcode)
    {
        $qrCode = QrCode::findOrFail($qrcode);
        
        // Verificar se o usuário pode editar este QR Code
        // Admins podem editar qualquer QR code, usuários normais apenas os seus
        if ($qrCode->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return view('qrcodes.edit', compact('qrCode'));
    }

    public function update(Request $request, $qrcode)
    {
        $qrCode = QrCode::findOrFail($qrcode);
        
        // Verificar se o usuário pode editar este QR Code
        // Admins podem editar qualquer QR code, usuários normais apenas os seus
        if ($qrCode->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'design' => 'nullable|string', // Aceita string JSON para design
            'design_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'design_background' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'design_size' => 'nullable|integer|min:100|max:2000',
            'design_shape' => 'nullable|string|in:square,round',
        ]);

        // Preparar dados de design se fornecidos
        $design = $request->input('design', []);
        if (is_string($design)) {
            $design = json_decode($design, true) ?: [];
        }

        // Se não veio como JSON, montar a partir dos campos individuais
        if (empty($design) && ($request->has('design_color') || $request->has('design_background') || $request->has('design_size') || $request->has('design_shape'))) {
            $design = [
                'colors' => [
                    'body' => $request->input('design_color', '#000000'),
                    'background' => $request->input('design_background', '#ffffff')
                ],
                'size' => (int) $request->input('design_size', 300),
                'shape' => $request->input('design_shape', 'square'),
                'margin' => 10
            ];
        }

        // Verificar se o design mudou (necessita regeneração)
        $needsRegeneration = false;
        if (!empty($design) && $qrCode->design !== $design) {
            $needsRegeneration = true;
        }

        // Atualizar dados do QR Code
        $updateData = [
            'name' => $request->name,
            'content' => $request->content,
        ];

        // Se design mudou, incluir na atualização
        if ($needsRegeneration) {
            $updateData['design'] = $design;
        }

        // Se precisa regenerar (design mudou), gerar nova imagem
        if ($needsRegeneration) {
            // Deletar arquivo antigo
            if ($qrCode->file_path) {
                $this->qrGenerator->deleteQrCodeFile($qrCode->file_path);
            }
            
            // Gerar novo arquivo com URL curta e novo design
            $filename = $this->qrGenerator->generateUniqueFilename();
            $shortUrl = url('/r/' . $qrCode->short_code);
            $filePath = $this->qrGenerator->generateAndSave($shortUrl, $filename, 'svg', $design);
            
            $updateData['file_path'] = $filePath;
        }

        $qrCode->update($updateData);

        return redirect()->route('qrcodes.show', $qrCode)
            ->with('success', 'QR Code atualizado com sucesso!');
    }

    public function toggleStatus(Request $request, $qrcode)
    {
        try {
            $qrCode = QrCode::findOrFail($qrcode);
            
            // Verificar se o usuário pode editar este QR Code
            if ($qrCode->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
            }

            // Validar dados de entrada
            $status = $request->input('status');
            if (!in_array($status, ['active', 'archived'])) {
                return response()->json(['success' => false, 'message' => 'Status inválido'], 400);
            }

            $qrCode->update(['status' => $status]);

            return response()->json([
                'success' => true,
                'message' => 'Status atualizado com sucesso!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao alterar status do QR Code', [
                'qr_code_id' => $qrcode,
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao alterar status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($qrcode, $format = 'png')
    {
        try {
            $qrCode = QrCode::findOrFail($qrcode);
            
            // Verificar se o usuário pode baixar este QR Code
            if ($qrCode->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                abort(403);
            }

            // Verificar se extensão GD está disponível para formatos de imagem
            $hasGdExtension = extension_loaded('gd');
            
            // Validar formato suportado baseado nas extensões disponíveis
            if ($hasGdExtension) {
                $supportedFormats = ['png', 'jpg', 'jpeg', 'svg'];
            } else {
                $supportedFormats = ['svg'];
                // Se não há GD, forçar SVG para formatos de imagem
                if (in_array($format, ['png', 'jpg', 'jpeg'])) {
                    $format = 'svg';
                }
            }
            
            if (!in_array($format, $supportedFormats)) {
                abort(400, 'Formato não suportado. Use: ' . implode(', ', $supportedFormats));
            }

            // Normalizar formato (jpg/jpeg -> jpg)
            if ($format === 'jpeg') {
                $format = 'jpg';
            }

            // Gerar QR Code com resolução ultra alta para download
            $shortUrl = url('/r/' . $qrCode->short_code);
            
            // Usar design do QR Code se disponível
            $design = null;
            if ($qrCode->design) {
                if (is_string($qrCode->design)) {
                    $design = json_decode($qrCode->design, true);
                } elseif (is_array($qrCode->design)) {
                    $design = $qrCode->design;
                }
            }
            
            // Gerar QR Code com resolução ultra alta
            $qrCodeData = $this->qrGenerator->generateHighResolutionDownload($shortUrl, $format, $design);

            // Verificar se os dados foram gerados com sucesso
            if (empty($qrCodeData)) {
                abort(500, 'Erro ao gerar dados do QR Code');
            }

            // Definir nome do arquivo para download
            $downloadName = $qrCode->name . '_' . strtoupper($format) . '.' . $format;
            
            // Definir headers apropriados para o formato
            $headers = [];
            if ($format === 'svg') {
                $headers['Content-Type'] = 'image/svg+xml';
            } elseif ($format === 'jpg') {
                $headers['Content-Type'] = 'image/jpeg';
            } else {
                $headers['Content-Type'] = 'image/png';
            }

            // Retornar dados diretamente com headers apropriados
            return response($qrCodeData, 200, $headers)
                ->header('Content-Disposition', 'attachment; filename="' . $downloadName . '"');
        } catch (\Exception $e) {
            \Log::error('Erro ao baixar QR Code', [
                'qr_code_id' => $qrcode,
                'format' => $format,
                'error' => $e->getMessage()
            ]);
            
            abort(500, 'Erro ao gerar arquivo para download: ' . $e->getMessage());
        }
    }

    public function duplicate($qrcode)
    {
        $originalQrCode = QrCode::findOrFail($qrcode);
        
        // Verificar se o usuário pode duplicar este QR Code
        if ($originalQrCode->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'Acesso negado'], 403);
        }

        // Gerar novo código curto único
        $shortCode = $this->generateUniqueShortCode();
        
        // Gerar nome único para o arquivo
        $filename = $this->qrGenerator->generateUniqueFilename();
        
        // Gerar URL curta para redirecionamento
        $shortUrl = url('/r/' . $shortCode);
        
        // Usar design do QR Code original
        $design = $originalQrCode->design ?: [
            'colors' => [
                'body' => '#000000',
                'background' => '#ffffff'
            ],
            'size' => 300,
            'margin' => 10,
            'shape' => 'square'
        ];
        
        // Gerar e salvar o QR Code
        $filePath = $this->qrGenerator->generateAndSave($shortUrl, $filename, 'svg', $design);
        
        $newQrCode = auth()->user()->qrCodes()->create([
            'name' => $originalQrCode->name . ' (Cópia)',
            'type' => $originalQrCode->type,
            'content' => $originalQrCode->content,
            'short_code' => $shortCode,
            'file_path' => $filePath,
            'design' => $design,
            'status' => 'active',
            'is_dynamic' => $originalQrCode->is_dynamic,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'QR Code duplicado com sucesso!',
            'qr_code_id' => $newQrCode->id
        ]);
    }

    public function destroy($qrcode)
    {// Buscar o QR code manualmente
        $qrCode = QrCode::findOrFail($qrcode);
        
        if (!$qrCode) {
            \Log::error('QR Code not found', ['qr_code_id' => $qrcode]);
            abort(404, 'QR Code não encontrado');
        }
        
        // Log para debug
        \Log::info('QR Code destroy called', [
            'qr_code_id' => $qrCode->id,
            'qr_code_name' => $qrCode->name,
            'user_id' => auth()->id(),
            'qr_code_user_id' => $qrCode->user_id,
            'is_admin' => auth()->user()->hasRole('admin'),
            'request_method' => request()->method(),
            'request_url' => request()->url(),
            'request_data' => request()->all()
        ]);

        // Verificar se o usuário pode deletar este QR Code
        // Admins podem deletar qualquer QR code, usuários normais apenas os seus
        if ($qrCode->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            \Log::warning('Access denied for QR code deletion', [
                'qr_code_id' => $qrCode->id,
                'user_id' => auth()->id(),
                'qr_code_user_id' => $qrCode->user_id
            ]);
            abort(403);
        }

        // Deletar arquivo do QR Code
        if ($qrCode->file_path) {
            $this->qrGenerator->deleteQrCodeFile($qrCode->file_path);
        }

        $qrCode->delete();
        
        \Log::info('QR Code deleted successfully', [
            'qr_code_id' => $qrCode->id,
            'user_id' => auth()->id()
        ]);

        // Log para debug da detecção AJAX
        \Log::info('Checking AJAX request', [
            'is_ajax' => request()->ajax(),
            'wants_json' => request()->wantsJson(),
            'accept_header' => request()->header('Accept'),
            'content_type' => request()->header('Content-Type'),
            'method' => request()->method()
        ]);

        // Retornar JSON para requisições AJAX ou DELETE
        if (request()->ajax() || request()->wantsJson() || request()->method() === 'DELETE') {
            \Log::info('Returning JSON response for delete');
            return response()->json([
                'success' => true,
                'message' => 'QR Code deletado com sucesso!'
            ]);
        }

        // Redirecionar baseado no tipo de usuário
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.qr-codes')
                ->with('success', 'QR Code deletado com sucesso!');
        }

        return redirect()->route('qrcodes.index')
            ->with('success', 'QR Code deletado com sucesso!');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'type' => 'required|string',
        ]);

        // Gerar preview do QR Code com URL curta
        $shortCode = $this->generateUniqueShortCode();
        $shortUrl = url('/r/' . $shortCode);
        $previewBase64 = $this->qrGenerator->generateBase64($shortUrl, 'svg');

        return response()->json([
            'success' => true,
            'preview_url' => $previewBase64,
            'content' => $request->content,
            'type' => $request->type
        ]);
    }

    public function previewQrCode($qrcode)
    {
        try {
            \Log::info('Preview QR Code requested', ['qr_code_id' => $qrcode]);
            
            $qrCode = QrCode::findOrFail($qrcode);
            \Log::info('QR Code found', ['qr_code_name' => $qrCode->name, 'file_path' => $qrCode->file_path]);
            
            // Verificar se o usuário pode ver este QR Code
            if ($qrCode->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                \Log::warning('Access denied for QR Code preview', ['qr_code_id' => $qrcode, 'user_id' => auth()->id()]);
                abort(403);
            }

            // Se já existe arquivo, usar ele diretamente - SEM REGENERAÇÃO
            if ($qrCode->file_path && \Storage::disk('public')->exists($qrCode->file_path)) {
                $previewUrl = \Storage::disk('public')->url($qrCode->file_path);
                \Log::info('Using existing file for preview', ['preview_url' => $previewUrl]);
                
                // Retornar HTML com a imagem existente
                return response()->view('qrcodes.preview', [
                    'preview_url' => $previewUrl,
                    'qr_code' => $qrCode
                ]);
            } else {
                // Se não existe arquivo, gerar um temporário apenas para preview
                \Log::warning('QR Code file not found, generating temporary preview', [
                    'qr_code_id' => $qrCode->id,
                    'file_path' => $qrCode->file_path
                ]);
                
                $filename = 'temp_preview_' . $qrCode->id . '_' . time();
                $shortUrl = url('/r/' . $qrCode->short_code);
                
                // Usar design do QR Code se disponível
                $design = null;
                if ($qrCode->design) {
                    if (is_string($qrCode->design)) {
                        $design = json_decode($qrCode->design, true);
                    } elseif (is_array($qrCode->design)) {
                        $design = $qrCode->design;
                    }
                }
                
                $filePath = $this->qrGenerator->generateAndSave($shortUrl, $filename, 'svg', $design);
                $previewUrl = \Storage::disk('public')->url($filePath);
                \Log::info('Generated temporary preview', ['preview_url' => $previewUrl]);
                
                // Retornar HTML com a imagem temporária
                return response()->view('qrcodes.preview', [
                    'preview_url' => $previewUrl,
                    'qr_code' => $qrCode
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar preview do QR Code', [
                'qr_code_id' => $qrcode,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(500, 'Erro ao gerar preview do QR Code');
        }
    }

    private function generateUniqueShortCode(): string
    {
        do {
            $shortCode = Str::random(8);
        } while (QrCode::where('short_code', $shortCode)->exists());

        return $shortCode;
    }

    /**
     * Obter estatísticas do QR Code
     */
    private function getQrCodeStats(QrCode $qrCode): array
    {
        $totalScans = $qrCode->scans()->count();
        $uniqueScans = $qrCode->scans()->where('is_unique', true)->count();
        $todayScans = $qrCode->scans()->whereDate('scanned_at', today())->count();
        $thisWeekScans = $qrCode->scans()->whereBetween('scanned_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $thisMonthScans = $qrCode->scans()->whereMonth('scanned_at', now()->month)->count();

        // Estatísticas por dispositivo
        $deviceStats = $qrCode->scans()
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->pluck('count', 'device_type')
            ->toArray();

        // Estatísticas por país
        $countryStats = $qrCode->scans()
            ->whereNotNull('country')
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->pluck('count', 'country')
            ->toArray();

        // Último scan
        $lastScan = $qrCode->scans()->latest('scanned_at')->first();

        return [
            'total_scans' => $totalScans,
            'unique_scans' => $uniqueScans,
            'today_scans' => $todayScans,
            'this_week_scans' => $thisWeekScans,
            'this_month_scans' => $thisMonthScans,
            'device_stats' => $deviceStats,
            'country_stats' => $countryStats,
            'last_scan' => $lastScan,
        ];
    }

    /**
     * Move QR code to folder
     */
    public function moveToFolder(Request $request, $qrcodeId)
    {
        try {
            // Buscar o QR code
            $qrcode = QrCode::findOrFail($qrcodeId);
            
            // Verificar se o QR code pertence ao usuário
            if ($qrcode->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code não encontrado.'
                ], 404);
            }

            $request->validate([
                'folder_id' => 'nullable|exists:folders,id',
            ]);

            // Verificar se a pasta pertence ao usuário
            if ($request->folder_id) {
                $folder = auth()->user()->folders()->find($request->folder_id);
                if (!$folder) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pasta não encontrada.'
                    ], 404);
                }
            }

            $qrcode->update(['folder_id' => $request->folder_id]);

            return response()->json([
                'success' => true,
                'message' => 'QR Code movido com sucesso!',
                'folder_name' => $qrcode->fresh()->folder ? $qrcode->fresh()->folder->name : 'Sem Pasta'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error moving QR code to folder', [
                'error' => $e->getMessage(),
                'qr_code_id' => $qrcodeId,
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro interno do servidor.'
            ], 500);
        }
    }

    /**
     * Move multiple QR codes to folder
     */
    public function moveMultipleToFolder(Request $request)
    {
        $request->validate([
            'qr_code_ids' => 'required|array',
            'qr_code_ids.*' => 'exists:qr_codes,id',
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $user = auth()->user();
        $qrCodes = $user->qrCodes()->whereIn('id', $request->qr_code_ids);

        // Verificar se a pasta pertence ao usuário
        if ($request->folder_id) {
            $folder = $user->folders()->find($request->folder_id);
            if (!$folder) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pasta não encontrada.'
                ], 404);
            }
        }

        $updated = $qrCodes->update(['folder_id' => $request->folder_id]);

        return response()->json([
            'success' => true,
            'message' => "{$updated} QR Code(s) movido(s) com sucesso!",
            'moved_count' => $updated
        ]);
    }

    /**
     * Generate QR code preview for creation page
     */
    public function generatePreview(Request $request)
    {
        try {
            $request->validate([
                'url' => 'required|url',
                'size' => 'nullable|integer|min:50|max:500'
            ]);

            $url = $request->input('url');
            $size = $request->input('size', 80);

            // Generate unique filename for preview
            $filename = 'preview_' . time() . '_' . Str::random(10);
            
            // Generate QR code
            $filePath = $this->qrGenerator->generateAndSave($url, $filename, 'svg', [
                'colors' => [
                    'body' => '#000000',
                    'background' => '#ffffff'
                ],
                'size' => $size,
                'margin' => 10,
                'shape' => 'square'
            ]);

            if ($filePath && \Storage::disk('public')->exists($filePath)) {
                $qrCodeUrl = \Storage::url($filePath);
                
                return response()->json([
                    'success' => true,
                    'qr_code_url' => $qrCodeUrl,
                    'message' => 'Preview gerado com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar preview do QR Code'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar preview do QR Code', [
                'url' => $request->input('url'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar preview: ' . $e->getMessage()
            ], 500);
        }
    }
}