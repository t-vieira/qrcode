<?php
// public/check-routes.php

echo "<h1>🔍 Verificação de Rotas</h1>";

echo "<h2>1. Teste de Bootstrap</h2>";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "<p style='color: green;'>✅ Laravel carregado com sucesso!</p>";
    
    // Verificar arquivos de rotas disponíveis
    $routeFiles = [
        'routes/web.php',
        'routes/web-working.php', 
        'routes/web-minimal.php'
    ];
    
    echo "<h3>Arquivos de Rotas:</h3>";
    foreach ($routeFiles as $file) {
        $fullPath = __DIR__ . '/../' . $file;
        if (file_exists($fullPath)) {
            echo "<p style='color: green;'>✅ {$file} - Existe</p>";
        } else {
            echo "<p style='color: red;'>❌ {$file} - Não existe</p>";
        }
    }
    
} catch (Throwable $e) {
    echo "<p style='color: red;'>❌ Erro: " . $e->getMessage() . "</p>";
}

echo "<h2>2. Teste de Rotas Específicas</h2>";
try {
    if (isset($app)) {
        $router = $app->make('router');
        $routes = $router->getRoutes();
        
        echo "<p><strong>Total de rotas:</strong> " . count($routes) . "</p>";
        
        // Verificar rotas específicas
        $testRoutes = [
            'qrcodes.create',
            'qrcodes.index', 
            'dashboard',
            'login'
        ];
        
        echo "<h3>Verificação de Rotas:</h3>";
        echo "<ul>";
        foreach ($testRoutes as $routeName) {
            $route = $routes->getByName($routeName);
            if ($route) {
                echo "<li style='color: green;'>✅ {$routeName} - " . $route->uri() . "</li>";
            } else {
                echo "<li style='color: red;'>❌ {$routeName} - Não encontrada</li>";
            }
        }
        echo "</ul>";
    }
} catch (Throwable $e) {
    echo "<p style='color: red;'>❌ Erro ao verificar rotas: " . $e->getMessage() . "</p>";
}

echo "<h2>3. Arquivos de Rotas Disponíveis</h2>";
$routeFiles = [
    'routes/web.php',
    'routes/web-working.php', 
    'routes/web-minimal.php'
];

foreach ($routeFiles as $file) {
    $fullPath = __DIR__ . '/../' . $file;
    if (file_exists($fullPath)) {
        echo "<p style='color: green;'>✅ {$file} - Existe</p>";
    } else {
        echo "<p style='color: red;'>❌ {$file} - Não existe</p>";
    }
}

echo "<h2>4. Conteúdo do bootstrap/app.php</h2>";
$bootstrapFile = __DIR__ . '/../bootstrap/app.php';
if (file_exists($bootstrapFile)) {
    $content = file_get_contents($bootstrapFile);
    if (strpos($content, 'web-working.php') !== false) {
        echo "<p style='color: green;'>✅ bootstrap/app.php está usando web-working.php</p>";
    } elseif (strpos($content, 'web-minimal.php') !== false) {
        echo "<p style='color: orange;'>⚠️ bootstrap/app.php está usando web-minimal.php</p>";
    } else {
        echo "<p style='color: red;'>❌ bootstrap/app.php não está usando arquivo esperado</p>";
    }
} else {
    echo "<p style='color: red;'>❌ bootstrap/app.php não existe</p>";
}

echo "<hr>";
echo "<p><strong>Timestamp:</strong> " . date('Y-m-d H:i:s') . "</p>";
?>
