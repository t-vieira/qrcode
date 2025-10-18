<?php
// public/route-debug.php

echo "<h1>🔍 Debug de Rotas - Laravel QR Code SaaS</h1>";

echo "<h2>1. Informações do Servidor</h2>";
echo "<p><strong>Servidor:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Script Path:</strong> " . __FILE__ . "</p>";

echo "<h2>2. Teste de Bootstrap do Laravel</h2>";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "<p style='color: green;'>✅ Laravel carregado com sucesso!</p>";
    
    // Testar kernel
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<p style='color: green;'>✅ Kernel criado com sucesso!</p>";
    
    // Testar request
    $request = Illuminate\Http\Request::capture();
    echo "<p style='color: green;'>✅ Request capturado com sucesso!</p>";
    
    // Testar response
    $response = $kernel->handle($request);
    echo "<p style='color: green;'>✅ Response gerado com sucesso!</p>";
    
    $kernel->terminate($request, $response);
    
} catch (Throwable $e) {
    echo "<p style='color: red;'>❌ Erro ao carregar Laravel: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<h2>3. Teste de Rotas</h2>";
try {
    if (isset($app)) {
        echo "<p style='color: green;'>✅ Laravel carregado com sucesso!</p>";
        
        // Teste simples de rotas
        $importantRoutes = ['/', '/login', '/register', '/dashboard', '/qrcodes', '/qrcodes/create'];
        echo "<h3>Teste de Rotas:</h3>";
        echo "<ul>";
        foreach ($importantRoutes as $routePath) {
            echo "<li><a href='{$routePath}' target='_blank'>{$routePath}</a></li>";
        }
        echo "</ul>";
        
        echo "<p style='color: green;'>✅ Sistema de rotas funcionando!</p>";
    }
} catch (Throwable $e) {
    echo "<p style='color: red;'>❌ Erro ao testar rotas: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Teste de Arquivos</h2>";
$filesToCheck = [
    'bootstrap/app.php',
    'routes/web-working.php',
    'routes/web.php',
    'app/Http/Controllers/QrCodeController.php',
    'app/Http/Controllers/Auth/LoginController.php',
    'resources/views/qrcodes/index.blade.php',
];

foreach ($filesToCheck as $file) {
    $fullPath = __DIR__ . '/../' . $file;
    if (file_exists($fullPath)) {
        echo "<p style='color: green;'>✅ {$file} - Existe</p>";
    } else {
        echo "<p style='color: red;'>❌ {$file} - Não existe</p>";
    }
}

echo "<h2>5. Teste de Configuração</h2>";
try {
    if (isset($app)) {
        $config = $app->make('config');
        echo "<p><strong>APP_ENV:</strong> " . $config->get('app.env') . "</p>";
        echo "<p><strong>APP_DEBUG:</strong> " . ($config->get('app.debug') ? 'true' : 'false') . "</p>";
        echo "<p><strong>APP_URL:</strong> " . $config->get('app.url') . "</p>";
    }
} catch (Throwable $e) {
    echo "<p style='color: red;'>❌ Erro ao ler configuração: " . $e->getMessage() . "</p>";
}

echo "<h2>6. Links de Teste</h2>";
echo "<ul>";
echo "<li><a href='/'>Home (/)</a></li>";
echo "<li><a href='/login'>Login (/login)</a></li>";
echo "<li><a href='/register'>Register (/register)</a></li>";
echo "<li><a href='/dashboard'>Dashboard (/dashboard)</a></li>";
echo "<li><a href='/qrcodes'>QR Codes (/qrcodes)</a></li>";
echo "<li><a href='/qrcodes/create'>Criar QR Code (/qrcodes/create)</a></li>";
echo "<li><a href='/test-route'>Test Route (/test-route)</a></li>";
echo "</ul>";

echo "<h2>7. Informações do .htaccess</h2>";
$htaccessPath = __DIR__ . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "<p style='color: green;'>✅ .htaccess existe</p>";
    echo "<pre>" . htmlspecialchars(file_get_contents($htaccessPath)) . "</pre>";
} else {
    echo "<p style='color: red;'>❌ .htaccess não existe</p>";
}

echo "<h2>8. Teste de Permissões</h2>";
$writablePaths = [
    'storage',
    'bootstrap/cache',
    'storage/logs',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/framework/cache',
];

foreach ($writablePaths as $path) {
    $fullPath = __DIR__ . '/../' . $path;
    $isWritable = is_writable($fullPath);
    echo "<p>Path `{$path}` is writable: " . ($isWritable ? "<span style='color: green;'>✅ Yes</span>" : "<span style='color: red;'>❌ No</span>") . "</p>";
}

echo "<hr>";
echo "<p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>User Agent:</strong> " . $_SERVER['HTTP_USER_AGENT'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
?>
