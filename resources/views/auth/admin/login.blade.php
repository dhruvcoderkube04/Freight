@extends('auth.base')

@section('title', 'Admin Login')

@section('content')
    <link
        href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
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
                            <h1 class="ark__title" style="color: #667eea;">Administrator Access</h1>
                            <p class="ark__subtitle">Secure login for admin users only. Regular users should use <a
                                    href="{{ route('login') }}"
                                    style="color: #667eea; text-decoration: underline;">front-end login</a>.</p>
                        </div>

                        @if (session('error'))
                            <div class="alert alert-danger ark__login-width" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.login') }}" class="ark__login-width">
                            @csrf

                            <div class="ark__form-group">
                                <label class="ark__label" for="email">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="ark__input" placeholder="admin@example.com" required autofocus />
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="ark__form-group">
                                <label class="ark__label" for="password">Password</label>
                                <input type="password" name="password" id="password" class="ark__input"
                                    placeholder="Enter your password" required />
                                @error('password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="ark__checkbox-wrap">
                                <div class="ark__checkbox-group">
                                    <input type="checkbox" name="remember" id="remember" class="ark__checkbox">
                                    <label for="remember">Remember Me</label>
                                </div>
                            </div>

                            <button type="submit" class="ark__submit-btn"
                                style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="fas fa-shield-alt"></i> Admin Log In
                            </button>

                            <p class="ark__login-link">
                                <a href="{{ route('login') }}">Back to User Login</a>
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
