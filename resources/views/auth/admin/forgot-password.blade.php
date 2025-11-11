@extends('auth.admin.base')

@section('title', 'Admin - Forgot Password')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">

<div class="ark__container">
    <div class="ark__form-section">
        <div class="ark__form--wrap">
            <div class="ark__form-bg">
                <div class="ark__form--center">

                    <div class="ark__logo ark__login-width" style="color: #667eea; font-weight: 700;">
                        <i class="fas fa-shield-alt"></i> Admin <span style="color: #764ba2;">Panel</span>
                    </div>

                    <div class="ark__auth-header ark__login-width">
                        <h1 class="ark__title" style="color: #667eea;">Forgot Password?</h1>
                        <p class="ark__subtitle">Enter your admin email to receive a password reset link.</p>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success ark__login-width" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.password.email') }}" class="ark__login-width">
                        @csrf

                        <div class="ark__form-group">
                            <label class="ark__label" for="email">Admin Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="ark__input" placeholder="admin@example.com" required autofocus />
                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="ark__submit-btn"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                            <i class="fas fa-paper-plane"></i> Send Reset Link
                        </button>

                        <p class="ark__login-link">
                            <a href="{{ route('admin.login') }}">Back to Admin Login</a>
                        </p>
                    </form>
                </div>

                <div class="ark__round-one"></div>
                <div class="ark__round-two"></div>
            </div>
        </div>
    </div>
</div>
@endsection