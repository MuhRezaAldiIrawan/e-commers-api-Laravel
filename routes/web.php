<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentRedirectController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/success', [PaymentRedirectController::class, 'success'])->name('payment.success');
Route::get('/payment/failed', [PaymentRedirectController::class, 'failed'])->name('payment.failed');
