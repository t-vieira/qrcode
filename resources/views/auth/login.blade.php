@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="space-y-6">
    <div class="text-center">
        <h2 class="text-3xl font-bold text-gray-900">
            Entre na sua conta
        </h2>
        <p class="mt-2 text-sm text-gray-600">
            Ou
            <a href="{{ route('register') }}" class="font-medium text-teal-600 hover:text-teal-500">
                crie uma nova conta
            </a>
        </p>
    </div>
        
    <form class="space-y-6" method="POST" action="{{ route('login') }}">
        @csrf
        
        <div class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('email') border-red-500 @enderror"
                       placeholder="Digite seu email"
                       value="{{ old('email') }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Senha</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-teal-500 @error('password') border-red-500 @enderror"
                       placeholder="Digite sua senha">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox"
                       class="h-4 w-4 text-teal-600 focus:ring-teal-500 border-gray-300 rounded">
                <label for="remember" class="ml-2 block text-sm text-gray-900">
                    Lembrar de mim
                </label>
            </div>

            <div class="text-sm">
                <a href="#" class="font-medium text-teal-600 hover:text-teal-500">
                    Esqueceu sua senha?
                </a>
            </div>
        </div>

        <div>
            <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Entrar
            </button>
        </div>
    </form>
</div>
@endsection
