<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar se o usuário tem acesso às funcionalidades avançadas
        if (!$user->canAccessAdvancedFeatures()) {
            // Se não tem acesso, redirecionar para página de upgrade
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sua assinatura expirou. Faça upgrade para continuar usando as funcionalidades avançadas.',
                    'subscription_status' => $user->subscription_status,
                    'trial_ends_at' => $user->trial_ends_at,
                ], 403);
            }

            return redirect()->route('subscription.upgrade')
                ->with('error', 'Sua assinatura expirou. Faça upgrade para continuar usando as funcionalidades avançadas.');
        }

        return $next($request);
    }
}
