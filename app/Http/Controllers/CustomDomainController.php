<?php

namespace App\Http\Controllers;

use App\Models\CustomDomain;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CustomDomainController extends Controller
{
    /**
     * Listar domínios customizados do usuário
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $domains = $user->customDomains()->latest()->get();
        
        return view('domains.index', compact('domains'));
    }

    /**
     * Mostrar formulário de criação
     */
    public function create()
    {
        return view('domains.create');
    }

    /**
     * Criar novo domínio customizado
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário pode adicionar mais domínios
        $maxDomains = $user->hasActiveSubscription() ? 5 : 1;
        $currentDomains = $user->customDomains()->count();
        
        if ($currentDomains >= $maxDomains) {
            return response()->json([
                'success' => false,
                'message' => "Você pode ter no máximo {$maxDomains} domínio(s) customizado(s).",
            ], 400);
        }

        $validated = $request->validate([
            'domain' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9][a-zA-Z0-9-]{0,61}[a-zA-Z0-9]?\.[a-zA-Z]{2,}$/',
                function ($attribute, $value, $fail) {
                    // Verificar se não é um domínio reservado
                    $reserved = ['www', 'api', 'admin', 'app', 'mail', 'ftp', 'blog', 'shop'];
                    $subdomain = explode('.', $value)[0];
                    
                    if (in_array($subdomain, $reserved)) {
                        $fail('Este subdomínio é reservado e não pode ser usado.');
                    }
                },
            ],
        ]);

        // Verificar se o domínio já existe
        $existingDomain = CustomDomain::where('domain', $validated['domain'])->first();
        if ($existingDomain) {
            return response()->json([
                'success' => false,
                'message' => 'Este domínio já está em uso.',
            ], 400);
        }

        // Gerar registro DNS para validação
        $dnsRecord = 'qrcode-verification=' . Str::random(32);
        
        $domain = CustomDomain::create([
            'user_id' => $user->id,
            'domain' => $validated['domain'],
            'status' => 'pending',
            'dns_record' => $dnsRecord,
        ]);

        Log::info("Domínio customizado criado: {$domain->domain} para usuário {$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'Domínio adicionado com sucesso! Configure o registro DNS para ativá-lo.',
            'domain' => $domain,
            'dns_record' => $dnsRecord,
        ]);
    }

    /**
     * Verificar status do domínio
     */
    public function verify(Request $request, CustomDomain $domain): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o domínio pertence ao usuário
        if ($domain->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Domínio não encontrado.',
            ], 404);
        }

        try {
            // Verificar se o registro DNS está configurado
            $dnsRecords = dns_get_record($domain->domain, DNS_TXT);
            $isVerified = false;
            
            foreach ($dnsRecords as $record) {
                if (isset($record['txt']) && $record['txt'] === $domain->dns_record) {
                    $isVerified = true;
                    break;
                }
            }

            if ($isVerified) {
                $domain->update([
                    'status' => 'verified',
                    'verified_at' => now(),
                ]);

                Log::info("Domínio verificado: {$domain->domain}");

                return response()->json([
                    'success' => true,
                    'message' => 'Domínio verificado com sucesso!',
                    'status' => 'verified',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Registro DNS não encontrado. Verifique se foi configurado corretamente.',
                    'status' => 'pending',
                ]);
            }

        } catch (\Exception $e) {
            Log::error("Erro ao verificar domínio {$domain->domain}: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar domínio. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Definir como domínio primário
     */
    public function setPrimary(Request $request, CustomDomain $domain): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o domínio pertence ao usuário
        if ($domain->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Domínio não encontrado.',
            ], 404);
        }

        // Verificar se o domínio está verificado
        if ($domain->status !== 'verified') {
            return response()->json([
                'success' => false,
                'message' => 'Domínio deve estar verificado para ser definido como primário.',
            ], 400);
        }

        // Remover primário de outros domínios
        $user->customDomains()->update(['is_primary' => false]);
        
        // Definir como primário
        $domain->update(['is_primary' => true]);

        Log::info("Domínio definido como primário: {$domain->domain} para usuário {$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'Domínio definido como primário com sucesso!',
        ]);
    }

    /**
     * Remover domínio customizado
     */
    public function destroy(Request $request, CustomDomain $domain): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o domínio pertence ao usuário
        if ($domain->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Domínio não encontrado.',
            ], 404);
        }

        $domainName = $domain->domain;
        $domain->delete();

        Log::info("Domínio customizado removido: {$domainName} do usuário {$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'Domínio removido com sucesso!',
        ]);
    }

    /**
     * Obter instruções de configuração DNS
     */
    public function instructions(Request $request, CustomDomain $domain)
    {
        $user = $request->user();
        
        // Verificar se o domínio pertence ao usuário
        if ($domain->user_id !== $user->id) {
            abort(404);
        }

        return view('domains.instructions', compact('domain'));
    }
}
