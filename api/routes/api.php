<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Public Routes
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected Routes (requires valid Sanctum Token)
Route::middleware('auth:sanctum')->group(function () {
    // Auth management
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // RBAC test routes

    // Only Admins
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Welcome to the Admin Dashboard']);
        });
    });

    // Admins and Secreatries
    Route::middleware('role:admin,secretary')->group(function () {
        Route::get('/secretary/overview', function () {
            return response()->json(['message' => 'Welcome to the Secretary Overview']);
        });
    });
});