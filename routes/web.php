<?php

use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Public Routes (Unauthenticated)
Route::get('/', function () {
    return redirect('/login');
});

// Authentication Routes
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Social Login Routes
Route::get('/auth/{provider}', [SocialLoginController::class, 'redirectToProvider'])->name('social.login');
Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'handleProviderCallback']);

// Protected Routes - Require Authentication
Route::middleware('auth')->group(function () {
    // Shipment Routes
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/shipments/step1', [ShipmentController::class, 'storeStep1'])->name('shipments.store.step1');
    Route::post('/shipments/step2', [ShipmentController::class, 'storeStep2'])->name('shipments.store.step2');
    Route::post('/shipments/step3', [ShipmentController::class, 'storeStep3'])->name('shipments.store.step3');
    Route::post('/shipments/step4', [ShipmentController::class, 'storeStep4'])->name('shipments.store.step4');

    // Quote Management Routes
    Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::get('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    
    // Payment Routes
    Route::get('/quotes/{id}/payment', [QuoteController::class, 'showPaymentForm'])->name('quotes.payment.form');
    Route::post('/quotes/{id}/payment/process', [QuoteController::class, 'processPayment'])->name('quotes.payment.process');
    Route::get('/payments/{payment}/status', [QuoteController::class, 'paymentStatus'])->name('payments.status');
    Route::get('/payments/{payment}/process', [PaymentController::class, 'processStripePayment'])->name('payments.process');
    Route::get('/payments/{payment}/success', [PaymentController::class, 'paymentSuccess'])->name('payments.success');
    Route::get('/payments/{payment}/cancel', [PaymentController::class, 'paymentCancel'])->name('payments.cancel');

    // Dashboard
    Route::get('/dashboard', function () {
        return redirect('/quotes');
    })->name('dashboard');

    // Redirect root to quotes when authenticated
    Route::get('/', function () {
        return redirect('/quotes');
    });
});