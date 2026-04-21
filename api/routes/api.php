<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; //Imports the AuthController

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (require Sanctum authentication)
Route::middleware('auth:sactum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

// Existing health check route (can be public or protected based on requirements, will see)
Route::get('/health', function () {
    return response()->json(['status' => 'Laravel API is running']);
});