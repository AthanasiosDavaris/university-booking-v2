<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReservationController;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (require Sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Buildings (Read-only for authenticated users)
    Route::get('/buildings', [BuildingController::class, 'index']);
    Route::get('/buildings/{building}',[BuildingController::class, 'show']);

    // Rooms (Read-only for authenticated users)
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::get('/rooms/{room}', [RoomController::class, 'show']);

    // Reservations
    Route::post('/reservations',[ReservationController::class, 'store']);
});

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'Laravel API is running']);
});