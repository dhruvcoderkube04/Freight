<?php

use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', fn() => redirect('/login'));

// ============================================================================
// FRONT-END ROUTES (Regular Users Only)
// ============================================================================

Route::controller(AuthController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->name('logout');

    Route::get('/forgot-password', 'showForgotForm')->name('password.request');
    Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');
    Route::get('/reset-password/{token}', 'showResetForm')->name('password.reset');
    Route::post('/reset-password', 'reset')->name('password.update');

    Route::get('auth/{provider}/redirect', 'redirectToProvider')->name('social.redirect');
    Route::get('auth/{provider}/callback', 'handleProviderCallback')->name('social.callback');
});

Route::middleware(['auth', 'user', 'session.timeout'])->group(function () {
    Route::get('/quotes/index', [QuoteController::class, 'index'])->name('quotes.index');
    Route::post('/quotes/store', [QuoteController::class, 'storeQuote'])->name('quotes.store');
    Route::any('/quotes/{quote}', [QuoteController::class, 'show'])->name('quotes.show');
    Route::post('/quotes/{id}/payment', [QuoteController::class, 'showPaymentForm'])->name('quotes.payment.form');
    Route::post('/quotes/{id}/payment/process', [QuoteController::class, 'processPayment'])->name('quotes.payment.process');
    Route::get('/payments/{payment}/status', [QuoteController::class, 'paymentStatus'])->name('payments.status');
    Route::get('/payments/{payment}/process', [PaymentController::class, 'processStripePayment'])->name('payments.process');
    Route::get('/payments/{payment}/success', [PaymentController::class, 'paymentSuccess'])->name('payments.success');
    Route::get('/payments/{payment}/cancel', [PaymentController::class, 'paymentCancel'])->name('payments.cancel');
});

// ============================================================================
// ADMIN ROUTES (Admin Users Only)
// ============================================================================

Route::prefix('admin')->group(function () {
    Route::get('/', fn() => Auth::check() && Auth::user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login')
    );

    // Forgot Password
    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPasswordForm'])->name('admin.password.request');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLinkEmail'])->name('admin.password.email');

    // Reset Password
    Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('admin.password.reset');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('admin.password.update');

    // Admin Login/Logout (Public) – Using AdminAuthController
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    
    Route::middleware(['auth', 'admin', 'session.timeout'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/quotes', [AdminController::class, 'quotes'])->name('admin.quotes');
        Route::get('/tql-responses', [AdminController::class, 'tqlResponses'])->name('admin.tql-responses');

        Route::get('/settings', [SiteSettingController::class, 'index'])->name('admin.settings');
        Route::post('/settings', [SiteSettingController::class, 'update'])->name('admin.settings.update');
    });
});