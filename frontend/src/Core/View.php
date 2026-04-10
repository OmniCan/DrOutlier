<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    private static ?Environment $twig = null;

    /**
     * Initialize Twig
     */
    public static function init(): void
    {
        $loader = new FilesystemLoader(__DIR__ . '/../../views');
        
        self::$twig = new Environment($loader, [
            'cache' => $_ENV['APP_ENV'] === 'production' 
                ? __DIR__ . '/../../storage/cache' 
                : false,
            'debug' => $_ENV['APP_DEBUG'] === 'true',
            'auto_reload' => true,
        ]);

        // Add global variables
        self::$twig->addGlobal('app_name', $_ENV['APP_NAME']);
        self::$twig->addGlobal('app_url', $_ENV['APP_URL']);
        
        // Add custom filters/functions
        self::addCustomFunctions();
    }

    /**
     * Render a Twig template
     */
    public static function render(string $template, array $data = []): void
    {
        // Add auth user to all views
        $data['user'] = $_SESSION['user'] ?? null;
        $data['auth'] = isset($_SESSION['user']);
        
        echo self::$twig->render($template, $data);
    }

    /**
     * Return rendered HTML without echoing
     */
    public static function make(string $template, array $data = []): string
    {
        $data['user'] = $_SESSION['user'] ?? null;
        $data['auth'] = isset($_SESSION['user']);
        
        return self::$twig->render($template, $data);
    }

    /**
     * Add custom Twig functions and filters
     */
    private static function addCustomFunctions(): void
    {
        // asset() function for static files
        $assetFunction = new \Twig\TwigFunction('asset', function ($path) {
            return $_ENV['APP_URL'] . '/' . ltrim($path, '/');
        });
        self::$twig->addFunction($assetFunction);

        // url() function for generating URLs
        $urlFunction = new \Twig\TwigFunction('url', function ($path = '') {
            return $_ENV['APP_URL'] . '/' . ltrim($path, '/');
        });
        self::$twig->addFunction($urlFunction);

        // old() function for form repopulation
        $oldFunction = new \Twig\TwigFunction('old', function ($key, $default = '') {
            return $_SESSION['old'][$key] ?? $default;
        });
        self::$twig->addFunction($oldFunction);
    }
}
