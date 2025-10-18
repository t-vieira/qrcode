<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Listar equipes do usuário
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $ownedTeams = $user->ownedTeams()->with('users')->get();
        $memberTeams = $user->teams()->with('owner')->get();
        
        return view('teams.index', compact('ownedTeams', 'memberTeams'));
    }

    /**
     * Mostrar formulário de criação
     */
    public function create()
    {
        return view('teams.create');
    }

    /**
     * Criar nova equipe
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário pode criar mais equipes
        $maxTeams = $user->hasActiveSubscription() ? 10 : 1;
        $currentTeams = $user->ownedTeams()->count();
        
        if ($currentTeams >= $maxTeams) {
            return response()->json([
                'success' => false,
                'message' => "Você pode ter no máximo {$maxTeams} equipe(s).",
            ], 400);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team = Team::create([
            'owner_id' => $user->id,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'status' => 'active',
        ]);

        // Adicionar o criador como membro da equipe
        $team->users()->attach($user->id, [
            'role' => 'owner',
            'permissions' => json_encode(['*']), // Todas as permissões
        ]);

        Log::info("Equipe criada: {$team->name} pelo usuário {$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'Equipe criada com sucesso!',
            'team' => $team,
        ]);
    }

    /**
     * Mostrar detalhes da equipe
     */
    public function show(Request $request, Team $team)
    {
        $user = $request->user();
        
        // Verificar se o usuário é membro da equipe
        if (!$team->users()->where('user_id', $user->id)->exists()) {
            abort(403, 'Você não tem acesso a esta equipe.');
        }

        $team->load(['users', 'owner', 'qrCodes']);
        $members = $team->users()->withPivot('role', 'permissions')->get();
        
        return view('teams.show', compact('team', 'members'));
    }

    /**
     * Mostrar formulário de edição
     */
    public function edit(Request $request, Team $team)
    {
        $user = $request->user();
        
        // Verificar se o usuário é o dono da equipe
        if ($team->owner_id !== $user->id) {
            abort(403, 'Apenas o dono da equipe pode editá-la.');
        }

        return view('teams.edit', compact('team'));
    }

    /**
     * Atualizar equipe
     */
    public function update(Request $request, Team $team): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário é o dono da equipe
        if ($team->owner_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas o dono da equipe pode editá-la.',
            ], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        Log::info("Equipe atualizada: {$team->name} pelo usuário {$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'Equipe atualizada com sucesso!',
            'team' => $team,
        ]);
    }

    /**
     * Adicionar membro à equipe
     */
    public function addMember(Request $request, Team $team): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário é o dono da equipe
        if ($team->owner_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas o dono da equipe pode adicionar membros.',
            ], 403);
        }

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|string|in:member,admin',
            'permissions' => 'nullable|array',
        ]);

        $member = User::where('email', $validated['email'])->first();
        
        // Verificar se o usuário já é membro da equipe
        if ($team->users()->where('user_id', $member->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuário já é membro da equipe.',
            ], 400);
        }

        // Definir permissões padrão baseadas no role
        $permissions = $validated['permissions'] ?? $this->getDefaultPermissions($validated['role']);

        $team->users()->attach($member->id, [
            'role' => $validated['role'],
            'permissions' => json_encode($permissions),
        ]);

        Log::info("Membro adicionado à equipe: {$member->email} na equipe {$team->name}");

        return response()->json([
            'success' => true,
            'message' => 'Membro adicionado com sucesso!',
        ]);
    }

    /**
     * Remover membro da equipe
     */
    public function removeMember(Request $request, Team $team, User $member): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário é o dono da equipe ou o próprio membro
        if ($team->owner_id !== $user->id && $member->id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para remover este membro.',
            ], 403);
        }

        // Verificar se o membro pertence à equipe
        if (!$team->users()->where('user_id', $member->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuário não é membro da equipe.',
            ], 400);
        }

        $team->users()->detach($member->id);

        Log::info("Membro removido da equipe: {$member->email} da equipe {$team->name}");

        return response()->json([
            'success' => true,
            'message' => 'Membro removido com sucesso!',
        ]);
    }

    /**
     * Atualizar permissões do membro
     */
    public function updateMemberPermissions(Request $request, Team $team, User $member): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário é o dono da equipe
        if ($team->owner_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas o dono da equipe pode atualizar permissões.',
            ], 403);
        }

        $validated = $request->validate([
            'role' => 'required|string|in:member,admin',
            'permissions' => 'nullable|array',
        ]);

        // Verificar se o membro pertence à equipe
        if (!$team->users()->where('user_id', $member->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuário não é membro da equipe.',
            ], 400);
        }

        $permissions = $validated['permissions'] ?? $this->getDefaultPermissions($validated['role']);

        $team->users()->updateExistingPivot($member->id, [
            'role' => $validated['role'],
            'permissions' => json_encode($permissions),
        ]);

        Log::info("Permissões atualizadas: {$member->email} na equipe {$team->name}");

        return response()->json([
            'success' => true,
            'message' => 'Permissões atualizadas com sucesso!',
        ]);
    }

    /**
     * Deletar equipe
     */
    public function destroy(Request $request, Team $team): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário é o dono da equipe
        if ($team->owner_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas o dono da equipe pode deletá-la.',
            ], 403);
        }

        $teamName = $team->name;
        $team->delete();

        Log::info("Equipe deletada: {$teamName} pelo usuário {$user->id}");

        return response()->json([
            'success' => true,
            'message' => 'Equipe deletada com sucesso!',
        ]);
    }

    /**
     * Sair da equipe
     */
    public function leave(Request $request, Team $team): JsonResponse
    {
        $user = $request->user();
        
        // Verificar se o usuário é membro da equipe
        if (!$team->users()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Você não é membro desta equipe.',
            ], 400);
        }

        // Verificar se não é o dono da equipe
        if ($team->owner_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'O dono da equipe não pode sair. Transfira a propriedade primeiro.',
            ], 400);
        }

        $team->users()->detach($user->id);

        Log::info("Usuário saiu da equipe: {$user->email} da equipe {$team->name}");

        return response()->json([
            'success' => true,
            'message' => 'Você saiu da equipe com sucesso!',
        ]);
    }

    /**
     * Obter permissões padrão baseadas no role
     */
    protected function getDefaultPermissions(string $role): array
    {
        return match ($role) {
            'admin' => ['view', 'create', 'edit', 'delete'],
            'member' => ['view', 'create'],
            default => ['view'],
        };
    }
}
