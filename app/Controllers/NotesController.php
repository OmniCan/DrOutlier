<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\ApiService;

class NotesController
{
    private ApiService $api;

    public function __construct()
    {
        $this->api = new ApiService();
    }

    /**
     * Display all notes
     */
    public function index(): void
    {
        $response = $this->api->get('/notes');

        View::render('notes/index.twig', [
            'title' => 'Theory Notes',
            'notes' => $response['data'] ?? [],
            'categories' => $response['categories'] ?? []
        ]);
    }

    /**
     * Display a single note
     */
    public function show(string $id): void
    {
        $response = $this->api->get("/notes/{$id}");

        if (isset($response['error'])) {
            http_response_code(404);
            View::render('errors/404.twig');
            return;
        }

        View::render('notes/show.twig', [
            'title' => $response['data']['title'] ?? 'Note',
            'note' => $response['data']
        ]);
    }
}
