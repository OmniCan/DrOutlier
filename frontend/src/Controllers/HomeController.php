<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\ApiService;

class HomeController
{
    private ApiService $api;

    public function __construct()
    {
        $this->api = new ApiService();
    }

    /**
     * Display the homepage
     */
    public function index(): void
    {
        // Fetch data from Laravel API
        $featuredContent = $this->api->get('/featured-content');
        $stats = $this->api->get('/stats');

        View::render('home.twig', [
            'title' => 'Welcome to DrOutlier',
            'featured' => $featuredContent['data'] ?? [],
            'stats' => $stats['data'] ?? []
        ]);
    }
}
