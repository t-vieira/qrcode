<?php
echo "<h1>Diagnóstico do Sistema</h1>";

// Test 1: PHP básico
echo "<h2>1. PHP Básico</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script name: " . $_SERVER['SCRIPT_NAME'] . "<br>";

// Test 2: Laravel
echo "<h2>2. Laravel</h2>";
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "✅ Laravel carregado com sucesso<br>";
    
    // Test routes
    $routes = $app->make('router')->getRoutes();
    echo "✅ Rotas carregadas: " . count($routes) . "<br>";
    
    // Test specific routes
    $dashboardRoute = null;
    $loginRoute = null;
    foreach ($routes as $route) {
        if ($route->uri() === 'dashboard') {
            $dashboardRoute = $route;
        }
        if ($route->uri() === 'login') {
            $loginRoute = $route;
        }
    }
    
    if ($dashboardRoute) {
        echo "✅ Dashboard route encontrada: " . $dashboardRoute->uri() . "<br>";
        echo "   Controller: " . $dashboardRoute->getActionName() . "<br>";
        echo "   Middleware: " . implode(', ', $dashboardRoute->gatherMiddleware()) . "<br>";
    } else {
        echo "❌ Dashboard route NÃO encontrada!<br>";
    }
    
    if ($loginRoute) {
        echo "✅ Login route encontrada: " . $loginRoute->uri() . "<br>";
    } else {
        echo "❌ Login route NÃO encontrada!<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao carregar Laravel: " . $e->getMessage() . "<br>";
}

// Test 3: Environment
echo "<h2>3. Environment</h2>";
echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'not set') . "<br>";
echo "APP_DEBUG: " . ($_ENV['APP_DEBUG'] ?? 'not set') . "<br>";
echo "APP_URL: " . ($_ENV['APP_URL'] ?? 'not set') . "<br>";

// Test 4: Database
echo "<h2>4. Database</h2>";
try {
    $pdo = new PDO(
        'pgsql:host=' . ($_ENV['DB_HOST'] ?? 'localhost') . ';port=' . ($_ENV['DB_PORT'] ?? '5432') . ';dbname=' . ($_ENV['DB_DATABASE'] ?? 'qrcode'),
        $_ENV['DB_USERNAME'] ?? 'postgres',
        $_ENV['DB_PASSWORD'] ?? ''
    );
    echo "✅ Conexão com banco de dados OK<br>";
    
    // Test users table
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "✅ Usuários no banco: " . $userCount . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erro de banco de dados: " . $e->getMessage() . "<br>";
}

// Test 5: File permissions
echo "<h2>5. Permissões de Arquivo</h2>";
$storagePath = __DIR__ . '/../storage';
$bootstrapPath = __DIR__ . '/../bootstrap/cache';
echo "Storage path exists: " . (is_dir($storagePath) ? '✅' : '❌') . "<br>";
echo "Storage writable: " . (is_writable($storagePath) ? '✅' : '❌') . "<br>";
echo "Bootstrap cache exists: " . (is_dir($bootstrapPath) ? '✅' : '❌') . "<br>";
echo "Bootstrap cache writable: " . (is_writable($bootstrapPath) ? '✅' : '❌') . "<br>";

echo "<h2>6. Teste de Rotas</h2>";
echo '<a href="/test-route">Test Route (pública)</a><br>';
echo '<a href="/test-auth">Test Auth (autenticada)</a><br>';
echo '<a href="/test-dashboard">Test Dashboard (autenticada)</a><br>';
echo '<a href="/login">Login</a><br>';
echo '<a href="/dashboard">Dashboard</a><br>';
?>
