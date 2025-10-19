<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Login') - QR CODE CREATOR</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    
    <!-- Scripts -->
    <script src="{{ mix('js/app.js') }}" defer></script>
    
    <style>
        .auth-bg {
            background: linear-gradient(135deg, #14b8a6 0%, #22c55e 100%);
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .qr-icon {
            background: linear-gradient(135deg, #14b8a6 0%, #22c55e 100%);
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen auth-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <!-- Logo -->
        <div class="absolute top-6 left-6">
            <div class="flex items-center">
                <div class="qr-icon w-10 h-10 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M3 3h6v6H3V3zm8 0h6v6h-6V3zM3 11h6v6H3v-6zm8 0h6v6h-6v-6z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-white">QR CODE CREATOR</h1>
            </div>
        </div>

        <!-- Auth Content -->
        <div class="max-w-md w-full">
            <div class="auth-card rounded-2xl shadow-2xl p-8">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
