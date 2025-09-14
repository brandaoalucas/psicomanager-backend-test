<?php

use Illuminate\Support\Facades\Route;

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'services' => [
            'mysql' => 'connected',
            'mongodb' => 'connected'
        ]
    ]);
});

// API routes are automatically registered via Route Attributes
// Check the controllers for route definitions using #[Route] attributes
