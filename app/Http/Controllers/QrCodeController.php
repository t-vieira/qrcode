<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class QrCodeController extends Controller
{
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
        
        $qrCode = $user->qrCodes()->create([
            'name' => $request->name,
            'type' => $request->type,
            'content' => $request->content,
            'short_code' => $shortCode,
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

        $qrCode->update([
            'name' => $request->name,
            'content' => $request->content,
        ]);

        return redirect()->route('qrcodes.show', $qrCode)
            ->with('success', 'QR Code atualizado com sucesso!');
    }

    public function destroy(QrCode $qrCode)
    {
        // Verificar se o usuário pode deletar este QR Code
        if ($qrCode->user_id !== auth()->id()) {
            abort(403);
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

        // Por enquanto, retornar uma mensagem
        return response()->json([
            'message' => 'Download em desenvolvimento',
            'format' => $format,
            'qr_code' => $qrCode->name
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate([
            'content' => 'required|string',
            'type' => 'required|string',
        ]);

        // Por enquanto, retornar uma resposta simples
        return response()->json([
            'message' => 'Preview em desenvolvimento',
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