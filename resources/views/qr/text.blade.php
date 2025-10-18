<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Conteúdo do QR Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Conteúdo do QR Code
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Aqui está o conteúdo que estava no QR Code
                </p>
            </div>
            
            <div class="bg-white shadow rounded-lg p-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ $content }}</pre>
                </div>
            </div>
            
            <div class="text-center">
                <button onclick="window.close()" class="btn-primary">
                    Fechar
                </button>
            </div>
        </div>
    </div>
</body>
</html>
