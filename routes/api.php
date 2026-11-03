<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ItemController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;

// Item API routes
Route::apiResource('items', ItemController::class);

// Customer API routes
Route::apiResource('customers', CustomerController::class);

// Order API routes
Route::apiResource('orders', OrderController::class);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
