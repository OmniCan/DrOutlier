<?php
// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>Debug Info</h1>";

try {
    echo "<h2>1. Testing Autoload</h2>";
    require_once __DIR__ . '/vendor/autoload.php';
    echo "✓ Autoload works<br>";

    echo "<h2>2. Testing Dotenv</h2>";
    if (class_exists('Dotenv\Dotenv')) {
        echo "✓ Dotenv class exists<br>";
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        echo "✓ .env loaded<br>";
        echo "API_BASE_URL: " . ($_ENV['API_BASE_URL'] ?? 'NOT SET') . "<br>";
    } else {
        echo "✗ Dotenv class not found<br>";
    }

    echo "<h2>3. Testing Twig</h2>";
    if (class_exists('Twig\Environment')) {
        echo "✓ Twig loaded<br>";
        
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/views');
        $twig = new \Twig\Environment($loader, [
            'cache' => __DIR__ . '/storage/cache',
            'debug' => true,
        ]);
        echo "✓ Twig environment created<br>";
        
        // Try rendering a simple template
        echo "✓ Twig is working<br>";
    } else {
        echo "✗ Twig not found<br>";
    }

    echo "<h2>4. Testing Router</h2>";
    if (class_exists('App\Core\Router')) {
        echo "✓ Router class exists<br>";
    } else {
        echo "✗ Router class not found<br>";
    }

    echo "<h2>5. Testing View</h2>";
    if (class_exists('App\Core\View')) {
        echo "✓ View class exists<br>";
        \App\Core\View::init();
        echo "✓ View initialized<br>";
    } else {
        echo "✗ View class not found<br>";
    }

    echo "<h2>6. Testing Template Rendering</h2>";
    echo "Attempting to render home.twig...<br>";
    
    $html = \App\Core\View::render('home.twig', [
        'app_name' => 'DrOutlier',
        'auth' => false,
    ]);
    
    echo "✓ Template rendered successfully!<br>";
    echo "<h3>Preview:</h3>";
    echo "<div style='border: 2px solid green; padding: 10px; max-height: 400px; overflow: auto;'>";
    echo htmlspecialchars(substr($html, 0, 500)) . "...";
    echo "</div>";

} catch (\Exception $e) {
    echo "<div style='background: #ffcccc; padding: 20px; border: 2px solid red;'>";
    echo "<h2>❌ ERROR FOUND:</h2>";
    echo "<strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>File:</strong> " . htmlspecialchars($e->getFile()) . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine() . "<br>";
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<a href='/'>Try Homepage</a> | <a href='/check.php'>Check PHP Setup</a>";
