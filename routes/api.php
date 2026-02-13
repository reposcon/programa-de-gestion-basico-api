<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController; // Importante añadirlo
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('roles', RoleController::class);

    Route::apiResource('users', UserController::class);

    Route::apiResource('categories', CategoryController::class);
    Route::put('categories/{id}/toggle', [CategoryController::class, 'toggle']);

    Route::apiResource('subcategories', SubcategoryController::class);
    Route::put('subcategories/{id}/toggle', [SubcategoryController::class, 'toggle']);

    Route::apiResource('products', ProductController::class);
    Route::put('products/{id}/toggle', [ProductController::class, 'toggle']);
    
});