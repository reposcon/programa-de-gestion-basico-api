<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TaxSettingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ReportController;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('tax-settings', [TaxSettingController::class, 'index']);
    Route::get('paymentmethods', [PaymentMethodController::class, 'index']);
    
    // RUTAS DE CAJA (Estructura Reports)
    Route::get('reports/status', [ReportController::class, 'checkStatus']);
    Route::post('reports/open', [ReportController::class, 'openSession']);
    Route::post('reports/close', [ReportController::class, 'closeSession']);
    Route::get('reports/history', [ReportController::class, 'getHistory']);

    Route::post('products/import', [ProductController::class, 'importExcel']);
    Route::get('/products/template', [ProductController::class, 'downloadTemplate']);
    Route::get('/sales/{id}/invoice', [SaleController::class, 'downloadInvoice']);

    // Recursos API
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('subcategories', SubcategoryController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('sales', SaleController::class);
    Route::apiResource('customers', CustomerController::class);

    // Toggles
    Route::put('categories/{id}/toggle', [CategoryController::class, 'toggle']);
    Route::put('subcategories/{id}/toggle', [SubcategoryController::class, 'toggle']);
    Route::put('products/{id}/toggle', [ProductController::class, 'toggle']);
    Route::put('customers/{id}/toggle', [CustomerController::class, 'toggle']);
});