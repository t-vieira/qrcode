<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se o usuário está autenticado
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Verificar se o usuário tem role de admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta área.');
        }

        return $next($request);
    }
}
