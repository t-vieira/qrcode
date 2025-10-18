<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\QrCode;
use App\Models\Subscription;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    /**
     * Dashboard administrativo
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('subscription_status', 'active')->count(),
            'trial_users' => User::where('subscription_status', 'trialing')->count(),
            'total_qr_codes' => QrCode::count(),
            'total_teams' => Team::count(),
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'authorized')->count(),
        ];

        // Usuários recentes
        $recent_users = User::orderBy('created_at', 'desc')->take(10)->get();

        // QR Codes mais populares
        $popular_qr_codes = QrCode::withCount('scans')
            ->orderBy('scans_count', 'desc')
            ->take(10)
            ->get();

        // Estatísticas por mês
        $monthly_stats = $this->getMonthlyStats();

        return view('admin.dashboard', compact('stats', 'recent_users', 'popular_qr_codes', 'monthly_stats'));
    }

    /**
     * Listar todos os usuários
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->with('roles')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Exibir usuário específico
     */
    public function showUser(User $user)
    {
        $user->load(['qrCodes', 'subscriptions', 'teams']);
        
        return view('admin.users.show', compact('user'));
    }

    /**
     * Formulário para criar usuário
     */
    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Criar novo usuário
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'subscription_status' => 'required|in:active,trialing,inactive',
            'trial_ends_at' => 'nullable|date|after:now',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'subscription_status' => $request->subscription_status,
            'trial_ends_at' => $request->trial_ends_at,
            'email_verified_at' => now(),
        ]);

        // Atribuir roles
        if ($request->filled('roles')) {
            $user->assignRole($request->roles);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Formulário para editar usuário
     */
    public function editUser(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Atualizar usuário
     */
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'subscription_status' => 'required|in:active,trialing,inactive',
            'trial_ends_at' => 'nullable|date|after:now',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'subscription_status' => $request->subscription_status,
            'trial_ends_at' => $request->trial_ends_at,
        ]);

        // Atualizar roles
        if ($request->filled('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Excluir usuário
     */
    public function destroyUser(User $user)
    {
        // Não permitir excluir o próprio usuário admin
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'Você não pode excluir sua própria conta.');
        }

        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'Usuário excluído com sucesso!');
    }

    /**
     * Listar assinaturas
     */
    public function subscriptions(Request $request)
    {
        $query = Subscription::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Listar QR Codes
     */
    public function qrCodes(Request $request)
    {
        $query = QrCode::with(['user', 'scans']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $qrCodes = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.qr-codes.index', compact('qrCodes'));
    }

    /**
     * Exibir QR Code específico
     */
    public function showQrCode(QrCode $qrCode)
    {
        $qrCode->load(['user', 'scans']);
        
        return view('admin.qr-codes.show', compact('qrCode'));
    }

    /**
     * Listar equipes
     */
    public function teams(Request $request)
    {
        $query = Team::with(['owner', 'users']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%")
                  ->orWhereHas('owner', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
        }

        $teams = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.teams.index', compact('teams'));
    }

    /**
     * Estatísticas do sistema
     */
    public function statistics()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('subscription_status', 'active')->count(),
                'trial' => User::where('subscription_status', 'trialing')->count(),
                'inactive' => User::where('subscription_status', 'inactive')->count(),
            ],
            'qr_codes' => [
                'total' => QrCode::count(),
                'this_month' => QrCode::whereMonth('created_at', now()->month)->count(),
                'this_year' => QrCode::whereYear('created_at', now()->year)->count(),
            ],
            'subscriptions' => [
                'total' => Subscription::count(),
                'active' => Subscription::where('status', 'authorized')->count(),
                'pending' => Subscription::where('status', 'pending')->count(),
                'cancelled' => Subscription::where('status', 'cancelled')->count(),
            ],
            'teams' => [
                'total' => Team::count(),
                'this_month' => Team::whereMonth('created_at', now()->month)->count(),
            ],
        ];

        // Gráficos de crescimento
        $growth_data = $this->getGrowthData();

        return view('admin.statistics', compact('stats', 'growth_data'));
    }

    /**
     * Obter estatísticas mensais
     */
    private function getMonthlyStats()
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = [
                'month' => $date->format('M Y'),
                'users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'qr_codes' => QrCode::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return $months;
    }

    /**
     * Obter dados de crescimento
     */
    private function getGrowthData()
    {
        return [
            'users_growth' => $this->calculateGrowth(User::class),
            'qr_codes_growth' => $this->calculateGrowth(QrCode::class),
            'subscriptions_growth' => $this->calculateGrowth(Subscription::class),
        ];
    }

    /**
     * Calcular crescimento percentual
     */
    private function calculateGrowth($model)
    {
        $current = $model::whereMonth('created_at', now()->month)->count();
        $previous = $model::whereMonth('created_at', now()->subMonth()->month)->count();

        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }
}
