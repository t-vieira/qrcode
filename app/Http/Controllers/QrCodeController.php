<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Services\QrCodeGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
    protected QrCodeGeneratorService $qrGenerator;

    public function __construct(QrCodeGeneratorService $qrGenerator)
    {
        $this->qrGenerator = $qrGenerator;
    }
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Buscar QR Codes do usuário
        $qrCodes = $user->qrCodes()->latest()->paginate(20);
        
        // Estatísticas básicas
        $stats = [
            'total' => $user->qrCodes()->count(),
            'active' => $user->qrCodes()->where('status', 'active')->count(),
            'archived' => $user->qrCodes()->where('status', 'archived')->count(),
            'total_scans' => 0, // Por enquanto
        ];
        
        return view('qrcodes.index', compact('qrCodes', 'stats'));
    }

    public function create()
    {
        return view('qrcodes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:url,vcard,text,email,phone,sms,wifi,location',
            'content' => 'required|string',
        ]);

        $user = $request->user();
        
        // Gerar código curto único
        $shortCode = $this->generateUniqueShortCode();
        
        // Gerar nome único para o arquivo
        $filename = $this->qrGenerator->generateUniqueFilename();
        
        // Gerar e salvar o QR Code
        $filePath = $this->qrGenerator->generateAndSave($request->content, $filename, 'png');
        
        $qrCode = $user->qrCodes()->create([
            'name' => $request->name,
            'type' => $request->type,
            'content' => $request->content,
            'short_code' => $shortCode,
            'file_path' => $filePath,
            'status' => 'active',
            'is_dynamic' => false, // Por enquanto, sempre estático
        ]);

        return redirect()->route('qrcodes.show', $qrCode)
            ->with('success', 'QR Code criado com sucesso!');
    }

    public function show(QrCode $qrCode)
    {
        // Verificar se o usuário pode acessar este QR Code
        if ($qrCode->user_id !== auth()->id()) {
            abort(403);
        }

        return view('qrcodes.show', compact('qrCode'));
    }

    public function edit(QrCode $qrCode)
    {
        // Verificar se o usuário pode editar este QR Code
        if ($qrCode->user_id !== auth()->id()) {
            abort(403);
        }

        return view('qrcodes.edit', compact('qrCode'));
    }

    public function update(Request $request, QrCode $qrCode)
    {
        // Verificar se o usuário pode editar este QR Code
        if ($qrCode->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Se o conteúdo mudou, regenerar o QR Code
        if ($qrCode->content !== $request->content) {
            // Deletar arquivo antigo
            if ($qrCode->file_path) {
                $this->qrGenerator->deleteQrCodeFile($qrCode->file_path);
            }
            
            // Gerar novo arquivo
            $filename = $this->qrGenerator->generateUniqueFilename();
            $filePath = $this->qrGenerator->generateAndSave($request->content, $filename, 'png');
            
            $qrCode->update([
                'name' => $request->name,
                'content' => $request->content,
                'file_path' => $filePath,
            ]);
        } else {
            $qrCode->update([
                'name' => $request->name,
            ]);
        }

        return redirect()->route('qrcodes.show', $qrCode)
            ->with('success', 'QR Code atualizado com sucesso!');
    }

    public function destroy(QrCode $qrCode)
    {
        // Verificar se o usuário pode deletar este QR Code
        if ($qrCode->user_id !== auth()->id()) {
            abort(403);
        }

        // Deletar arquivo do QR Code
        if ($qrCode->file_path) {
            $this->qrGenerator->deleteQrCodeFile($qrCode->file_path);
        }

        $qrCode->delete();

        return redirect()->route('qrcodes.index')
            ->with('success', 'QR Code deletado com sucesso!');
    }

    public function download(QrCode $qrCode, $format = 'png')
    {
        // Verificar se o usuário pode baixar este QR Code
        if ($qrCode->user_id !== auth()->id()) {
            abort(403);
        }

        // Se o formato solicitado for diferente do arquivo atual, gerar novo
        if ($format !== 'png' || !$qrCode->file_path) {
            $filename = $this->qrGenerator->generateUniqueFilename();
            $filePath = $this->qrGenerator->generateAndSave($qrCode->content, $filename, $format);
        } else {
            $filePath = $qrCode->file_path;
        }

        // Verificar se o arquivo existe
        if (!\Storage::disk('public')->exists($filePath)) {
            abort(404, 'Arquivo do QR Code não encontrado');
        }

        return \Storage::disk('public')->download($filePath, $qrCode->name . '.' . $format);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'type' => 'required|string',
        ]);

        // Gerar preview do QR Code
        $previewBase64 = $this->qrGenerator->generateBase64($request->content, 'png');

        return response()->json([
            'success' => true,
            'preview_url' => $previewBase64,
            'content' => $request->content,
            'type' => $request->type
        ]);
    }

    private function generateUniqueShortCode(): string
    {
        do {
            $shortCode = Str::random(8);
        } while (QrCode::where('short_code', $shortCode)->exists());

        return $shortCode;
    }
}