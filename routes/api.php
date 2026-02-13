<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('web')->post('/logout', [AuthController::class, 'logout']);

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);         // Obtener todos los usuarios
    Route::get('{id}', [UserController::class, 'show']);       // Obtener un usuario específico
    Route::post('/', [UserController::class, 'store']);        // Crear un nuevo usuario
    Route::put('{id}', [UserController::class, 'update']);     // Actualizar un usuario
    Route::delete('{id}', [UserController::class, 'destroy']); // Eliminar un usuario
});

// Rutas para categorías
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('{id}', [CategoryController::class, 'show']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('{id}', [CategoryController::class, 'update']);
    Route::delete('{id}', [CategoryController::class, 'destroy']);
    Route::put('/{id}/toggle', [CategoryController::class, 'toggle']);

});

// Rutas para subcategorías
Route::prefix('subcategories')->group(function () {
    Route::get('/', [SubcategoryController::class, 'index']);
    Route::get('{id}', [SubcategoryController::class, 'show']);
    Route::post('/', [SubcategoryController::class, 'store']);
    Route::put('{id}', [SubcategoryController::class, 'update']);
    Route::delete('{id}', [SubcategoryController::class, 'destroy']);
    Route::put('/{id}/toggle', [SubcategoryController::class, 'toggle']);
});

// Rutas para productos
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('{id}', [ProductController::class, 'show']);
    Route::post('/', [ProductController::class, 'store']);
    Route::put('{id}', [ProductController::class, 'update']);
    Route::delete('{id}', [ProductController::class, 'destroy']);
    Route::put('/{id}/toggle', [ProductController::class, 'toggle']);
});
