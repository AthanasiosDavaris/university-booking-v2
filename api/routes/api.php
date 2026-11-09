<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\RoomController;

/* Public Routes */
Route::post('/auth/login', [AuthController::class, 'login']);

/* Protected Routes (Requires Sanctum Bearer Token) */
Route::middleware('auth:sanctum')->group(function () {

    // Authentication
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Campus Infrastructure
    Route::get('/buildings', [BuildingController::class, 'index']);
    Route::get('/buildings/{building}', [BuildingController::class, 'show']);
    Route::get('/rooms/availability', [BookingController::class, 'availability']);
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::get('/rooms/{room}', [RoomController::class, 'show']);

    // Bookings & Concurrency Engine
    Route::post('/reservations', [BookingController::class, 'store']);

    // Role-Based Access Control (RBAC)
    // Only for Secretary and Admin
    Route::middleware('role:admin,secretary')->group(function () {
        Route::patch('/reservations/{reservation}/status', [BookingController::class, 'updateStatus']);
        Route::get('/secretary/overview', function () {
            return response()->json(['message' => 'Secretary & Admin overview authorized.']);
        });
    });

    // Only Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return response()->json(['message' => 'Admin dashboard authorized.']);
        });
    });
});