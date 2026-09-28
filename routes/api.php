<?php

use App\Http\Controllers\Customer\MidtransController;
use Illuminate\Support\Facades\Route;

// Midtrans payment notification (webhook) — stateless, no session/CSRF.
Route::post('/payment/notification', [MidtransController::class, 'handleCallback'])
    ->middleware('throttle:30,1')
    ->name('payment.notification');
