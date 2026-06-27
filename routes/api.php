<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\LoyaltyPointController;

// Rotas públicas
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Rotas protegidas por JWT
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Unidades
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);
    Route::post('/branches', [BranchController::class, 'store']);
    Route::put('/branches/{id}', [BranchController::class, 'update']);
    Route::delete('/branches/{id}', [BranchController::class, 'destroy']);

    // Produtos
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);
    Route::post('/products', [ProductController::class, 'store']);
    Route::put('/products/{id}', [ProductController::class, 'update']);
    Route::delete('/products/{id}', [ProductController::class, 'destroy']);

    // Estoque
    Route::get('/inventories', [InventoryController::class, 'index']);
    Route::get('/inventories/{branchId}/{productId}', [InventoryController::class, 'show']);
    Route::post('/inventories', [InventoryController::class, 'store']);
    Route::post('/inventories/reduce', [InventoryController::class, 'reduce']);

    // Pedidos
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);

    // Pagamentos
    Route::post('/payments/{orderId}', [PaymentController::class, 'processPayment']);
    Route::get('/payments/{orderId}', [PaymentController::class, 'show']);

    // Fidelização
    Route::get('/loyalty', [LoyaltyPointController::class, 'index']);
    Route::post('/loyalty/redeem', [LoyaltyPointController::class, 'redeem']);
});