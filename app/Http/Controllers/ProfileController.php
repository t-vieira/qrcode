<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Exibir o perfil do usuário
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Exibir o formulário de edição do perfil
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Atualizar informações básicas do perfil
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Se o email foi alterado, marcar como não verificado
        if ($user->wasChanged('email')) {
            $user->email_verified_at = null;
            $user->save();
            
            // Aqui você pode adicionar lógica para reenviar email de verificação
            // event(new Registered($user));
        }

        return redirect()->route('profile.show')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Exibir formulário para alterar senha
     */
    public function editPassword()
    {
        return view('profile.edit-password');
    }

    /**
     * Atualizar senha do usuário
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Senha alterada com sucesso!');
    }

    /**
     * Exibir configurações de conta
     */
    public function settings()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    /**
     * Excluir conta do usuário
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        
        // Aqui você pode adicionar lógica adicional antes de excluir
        // como cancelar assinaturas, exportar dados, etc.
        
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Sua conta foi excluída com sucesso.');
    }
}
