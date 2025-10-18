<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\QrCode;
use App\Models\QrScan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataPrivacyController extends Controller
{
    /**
     * Página de privacidade de dados
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('privacy.index', compact('user'));
    }

    /**
     * Exportar todos os dados do usuário
     */
    public function export(Request $request): JsonResponse
    {
        $user = $request->user();
        
        try {
            // Coletar todos os dados do usuário
            $userData = $this->collectUserData($user);
            
            // Gerar arquivo JSON
            $filename = 'user_data_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';
            $filepath = 'exports/' . $filename;
            
            // Salvar arquivo
            Storage::disk('local')->put($filepath, json_encode($userData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            Log::info("Dados exportados para usuário {$user->id}");
            
            return response()->json([
                'success' => true,
                'message' => 'Dados exportados com sucesso!',
                'download_url' => route('privacy.download', ['filename' => $filename]),
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao exportar dados do usuário: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao exportar dados. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Download do arquivo de exportação
     */
    public function download(string $filename)
    {
        $user = auth()->user();
        
        // Verificar se o arquivo pertence ao usuário
        if (!str_starts_with($filename, "user_data_{$user->id}_")) {
            abort(403, 'Arquivo não autorizado.');
        }

        $filepath = 'exports/' . $filename;
        
        if (!Storage::disk('local')->exists($filepath)) {
            abort(404, 'Arquivo não encontrado.');
        }

        return Storage::disk('local')->download($filepath, 'meus_dados_qrcode_saas.json');
    }

    /**
     * Solicitar exclusão de dados
     */
    public function requestDeletion(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'confirm_deletion' => 'required|accepted',
        ]);

        try {
            // Marcar conta para exclusão
            $user->update([
                'deletion_requested_at' => now(),
                'deletion_reason' => $validated['reason'],
            ]);

            // Enviar email de confirmação (implementar depois)
            // Mail::to($user->email)->send(new DeletionRequestedMail($user));

            Log::info("Solicitação de exclusão de dados para usuário {$user->id}", [
                'reason' => $validated['reason'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de exclusão enviada. Você receberá um email de confirmação em breve.',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao solicitar exclusão de dados: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar solicitação. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Cancelar solicitação de exclusão
     */
    public function cancelDeletion(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if (!$user->deletion_requested_at) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma solicitação de exclusão pendente.',
            ], 400);
        }

        try {
            $user->update([
                'deletion_requested_at' => null,
                'deletion_reason' => null,
            ]);

            Log::info("Solicitação de exclusão cancelada para usuário {$user->id}");

            return response()->json([
                'success' => true,
                'message' => 'Solicitação de exclusão cancelada com sucesso.',
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar exclusão de dados: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar solicitação. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Processar exclusão de dados (admin)
     */
    public function processDeletion(User $user): bool
    {
        try {
            DB::beginTransaction();

            // 1. Anonimizar dados de scans
            $this->anonymizeScans($user);

            // 2. Remover QR Codes e arquivos
            $this->removeQrCodes($user);

            // 3. Remover dados pessoais
            $this->removePersonalData($user);

            // 4. Remover conta
            $user->forceDelete();

            DB::commit();

            Log::info("Dados do usuário {$user->id} foram excluídos permanentemente");

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Erro ao processar exclusão de dados: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            return false;
        }
    }

    /**
     * Coletar todos os dados do usuário
     */
    protected function collectUserData(User $user): array
    {
        return [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at,
                'trial_ends_at' => $user->trial_ends_at,
                'subscription_status' => $user->subscription_status,
            ],
            'qr_codes' => $user->qrCodes()->with(['scans'])->get()->map(function ($qrCode) {
                return [
                    'id' => $qrCode->id,
                    'name' => $qrCode->name,
                    'type' => $qrCode->type,
                    'is_dynamic' => $qrCode->is_dynamic,
                    'content' => $qrCode->content,
                    'design' => $qrCode->design,
                    'created_at' => $qrCode->created_at,
                    'scans_count' => $qrCode->scans_count,
                    'scans' => $qrCode->scans->map(function ($scan) {
                        return [
                            'scanned_at' => $scan->scanned_at,
                            'country' => $scan->country,
                            'city' => $scan->city,
                            'device_type' => $scan->device_type,
                            'browser' => $scan->browser,
                            'os' => $scan->os,
                            'is_unique' => $scan->is_unique,
                        ];
                    }),
                ];
            }),
            'subscriptions' => $user->subscriptions->map(function ($subscription) {
                return [
                    'id' => $subscription->id,
                    'status' => $subscription->status,
                    'plan_name' => $subscription->plan_name,
                    'amount' => $subscription->amount,
                    'created_at' => $subscription->created_at,
                ];
            }),
            'teams' => $user->teams->map(function ($team) {
                return [
                    'id' => $team->id,
                    'name' => $team->name,
                    'role' => $team->pivot->role,
                    'permissions' => $team->pivot->permissions,
                ];
            }),
            'support_tickets' => $user->supportTickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'subject' => $ticket->subject,
                    'message' => $ticket->message,
                    'status' => $ticket->status,
                    'priority' => $ticket->priority,
                    'created_at' => $ticket->created_at,
                ];
            }),
            'export_info' => [
                'exported_at' => now()->toISOString(),
                'data_retention_policy' => 'Dados mantidos conforme política de retenção',
                'contact' => 'Para dúvidas sobre seus dados, entre em contato conosco.',
            ],
        ];
    }

    /**
     * Anonimizar dados de scans
     */
    protected function anonymizeScans(User $user): void
    {
        QrScan::whereHas('qrCode', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->update([
            'ip_address' => '0.0.0.0',
            'country' => 'Anonymized',
            'city' => 'Anonymized',
            'latitude' => null,
            'longitude' => null,
            'user_agent' => 'Anonymized',
        ]);
    }

    /**
     * Remover QR Codes e arquivos
     */
    protected function removeQrCodes(User $user): void
    {
        $qrCodes = $user->qrCodes;
        
        foreach ($qrCodes as $qrCode) {
            // Remover arquivo físico
            if ($qrCode->file_path && Storage::exists($qrCode->file_path)) {
                Storage::delete($qrCode->file_path);
            }
            
            // Remover QR Code
            $qrCode->forceDelete();
        }
    }

    /**
     * Remover dados pessoais
     */
    protected function removePersonalData(User $user): void
    {
        // Remover assinaturas
        $user->subscriptions()->delete();
        
        // Remover tickets de suporte
        $user->supportTickets()->delete();
        
        // Remover domínios customizados
        $user->customDomains()->delete();
        
        // Remover pastas
        $user->folders()->delete();
        
        // Remover da equipe
        $user->teams()->detach();
        
        // Remover equipes próprias
        $user->ownedTeams()->delete();
    }

    /**
     * Obter status de privacidade
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();
        
        return response()->json([
            'has_deletion_request' => !is_null($user->deletion_requested_at),
            'deletion_requested_at' => $user->deletion_requested_at,
            'data_retention_days' => 365, // Política de retenção
            'can_export' => true,
            'can_request_deletion' => true,
        ]);
    }
}
