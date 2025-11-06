<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'));

// Authentication Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');

    // Forgot / Reset Password
    Route::get('/forgot-password', 'showForgotForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
    Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset');
    Route::post('/reset-password', 'reset')->name('password.update');

    // Social login
    Route::get('/auth/{provider}', 'redirectToProvider')->name('social.login');
    Route::get('/auth/{provider}/callback', 'handleProviderCallback');
});

// Protected routes
Route::middleware(['auth', 'session.timeout'])->group(function () {
    Route::get('/quotes/index', [QuoteController::class, 'index'])->name('quotes.index');
    Route::post('/quotes/store', [QuoteController::class, 'storeQuote'])->name('quotes.store');

    Route::any('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::get('/quotes/{id}/payment', [QuoteController::class, 'showPaymentForm'])->name('quotes.payment.form');
    Route::post('/quotes/{id}/payment/process', [QuoteController::class, 'processPayment'])->name('quotes.payment.process');
    Route::get('/payments/{payment}/status', [QuoteController::class, 'paymentStatus'])->name('payments.status');
    Route::get('/payments/{payment}/process', [PaymentController::class, 'processStripePayment'])->name('payments.process');
    Route::get('/payments/{payment}/success', [PaymentController::class, 'paymentSuccess'])->name('payments.success');
    Route::get('/payments/{payment}/cancel', [PaymentController::class, 'paymentCancel'])->name('payments.cancel');
});
