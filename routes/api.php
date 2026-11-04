<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); // Limit login attempts to 5 per minute to prevent brute force attacks

// Protected routes that require authentication
Route::middleware('auth:sanctum')->group(function () {

    // Authenticated user routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Item API routes
    Route::apiResource('items', ItemController::class);

    // Customer API routes
    Route::apiResource('customers', CustomerController::class);

    // Order API routes
    Route::apiResource('orders', OrderController::class);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
