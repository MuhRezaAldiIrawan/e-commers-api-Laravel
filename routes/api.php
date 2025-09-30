<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\OrderController;

Route::middleware(['api.key'])->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

});
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);

Route::middleware(['api.key', 'auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    Route::post('/checkout', [CheckoutController::class, 'checkout']);

    Route::post('/payment/create-invoice', [PaymentController::class, 'createInvoice']);

    Route::get('/orders', [OrderController::class, 'history']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
});
