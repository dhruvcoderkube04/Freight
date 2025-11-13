<?php

use App\Http\Controllers\Admin\PaymentRequestController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\UserController;
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

    /* ---------------- Quotes Routes ---------------- */
    Route::prefix('quotes')->as('quotes.')->group(function () {
        Route::get('/index', [QuoteController::class, 'index'])->name('index');
        Route::post('/store', [QuoteController::class, 'storeQuote'])->name('store');
        Route::post('/{id}/payment', [QuoteController::class, 'showPaymentForm'])->name('payment.form');
        Route::post('/{quote}/request-approval', [QuoteController::class, 'requestApproval'])->name('request-approval');
        Route::get('/approved-bookings', [QuoteController::class, 'approvedBookings'])->name('approved');
        Route::post('/{id}/payment/process', [QuoteController::class, 'processPayment'])->name('payment.process');
    });

    /* ---------------- Payments Routes ---------------- */
    Route::prefix('payments')->as('payments.')->group(function () {
        Route::get('/{payment}/status', [QuoteController::class, 'paymentStatus'])->name('status');
        Route::get('/{payment}/process', [PaymentController::class, 'processStripePayment'])->name('process');
        Route::get('/{payment}/success', [PaymentController::class, 'paymentSuccess'])->name('success');
        Route::get('/{payment}/cancel', [PaymentController::class, 'paymentCancel'])->name('cancel');
    });
});


// ============================================================================
// ADMIN ROUTES (Admin Users Only)
// ============================================================================

Route::prefix('admin')->as('admin.')->group(function () {

    /* ---------------- Redirect Root ---------------- */
    Route::get('/', function () {
        return Auth::check() && Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('admin.login');
    });

    /* ---------------- Password Reset ---------------- */
    Route::controller(AdminAuthController::class)->group(function () {
        Route::get('/forgot-password', 'showForgotPasswordForm')->name('password.request');
        Route::post('/forgot-password', 'sendResetLinkEmail')->name('password.email');

        Route::get('/reset-password/{token}', 'showResetPasswordForm')->name('password.reset');
        Route::post('/reset-password', 'resetPassword')->name('password.update');
    });

    /* ---------------- Authentication ---------------- */
    Route::controller(AdminAuthController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login');
        Route::post('/logout', 'logout')->name('logout');
    });

    /* ---------------- Protected Routes ---------------- */
    Route::middleware(['auth', 'admin', 'session.timeout'])->group(function () {

        // Dashboard & General
        Route::controller(AdminController::class)->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/quotes', 'quotes')->name('quotes');
            Route::get('/tql-responses', 'tqlResponses')->name('tql-responses');
        });

        // CMS Settings
        Route::controller(SiteSettingController::class)->prefix('settings')->as('settings.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'update')->name('update');
        });

        // User Management
        Route::controller(UserController::class)->prefix('users')->as('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/data', 'data')->name('data');
            Route::post('/{user}/toggle-approval', 'approve')->name('toggle-approval');
        });

        // Payment Requests
        Route::controller(PaymentRequestController::class)->prefix('payment-requests')->as('payment-requests.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/data', 'data')->name('data');
            Route::post('/{paymentRequest}/update-status', 'updateStatus')->name('update-status');
        });
    });
});
