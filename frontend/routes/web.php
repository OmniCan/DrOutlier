<?php

/**
 * Web Routes
 * 
 * Define your application routes here
 */

use App\Controllers\HomeController;
use App\Controllers\NotesController;
use App\Controllers\SpottersController;
use App\Controllers\OsceController;
use App\Controllers\ProfileController;
use App\Controllers\SubscriptionController;

// Homepage
$router->get('/', [HomeController::class, 'index']);

// Notes
$router->get('/notes', [NotesController::class, 'index']);
$router->get('/notes/{id}', [NotesController::class, 'show']);

// Spotters
$router->get('/spotters', [SpottersController::class, 'index']);
$router->get('/spotters/{id}', [SpottersController::class, 'show']);

// OSCE
$router->get('/osce', [OsceController::class, 'index']);
$router->get('/osce/{id}', [OsceController::class, 'show']);

// Profile
$router->get('/profile', [ProfileController::class, 'index']);
$router->post('/profile/update', [ProfileController::class, 'update']);

// Subscription
$router->get('/subscription', [SubscriptionController::class, 'index']);
$router->get('/pricing', [SubscriptionController::class, 'pricing']);

// Add more routes as you migrate features...
