<?php
echo "PHP is working!<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP version: " . phpversion() . "<br>";

// Test Laravel
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "Laravel loaded successfully!<br>";
    
    // Test routes
    $routes = $app->make('router')->getRoutes();
    echo "Routes loaded: " . count($routes) . "<br>";
    
    // Test dashboard route
    $dashboardRoute = null;
    foreach ($routes as $route) {
        if ($route->uri() === 'dashboard') {
            $dashboardRoute = $route;
            break;
        }
    }
    
    if ($dashboardRoute) {
        echo "Dashboard route found: " . $dashboardRoute->uri() . "<br>";
        echo "Dashboard controller: " . $dashboardRoute->getActionName() . "<br>";
    } else {
        echo "Dashboard route NOT found!<br>";
    }
    
} catch (Exception $e) {
    echo "Laravel error: " . $e->getMessage() . "<br>";
}
?>
