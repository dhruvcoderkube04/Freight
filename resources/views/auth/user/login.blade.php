@extends('auth.base')

@section('title', 'Ark Login')

@section('content')
    <!-- Google Fonts: Inter & DM Sans -->
    <link
        href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <div class="ark__container">
        {{-- Left Image Section (optional) --}}
        {{-- <div class="ark__image-section">
        <div class="ark__image-overlay"></div>
    </div> --}}

        {{-- Right Form Section --}}
        <div class="ark__form-section">
            <div class="ark__form--wrap">
                <div class="ark__form-bg">
                    <div class="ark__form--center">

                        {{-- Logo / Header --}}
                        <div class="ark__logo ark__login-width">Log<span>in</span></div>
                        <div class="ark__auth-header ark__login-width">
                            <h1 class="ark__title">Welcome Back!</h1>
                            <p class="ark__subtitle">Log in to access your account and manage your Quotes.</p>

                        </div>

                        {{-- Social Login Buttons --}}
                        <div class="ark__social-buttons ark__login-width">
                            <a href="{{ route('social.redirect', ['provider' => 'google']) }}"
                                class="ark__social-btn ark__google">
                                <div class="ark__icon-wrap">
                                    <img src="{{ asset('assets/images/GoogleIcon.svg') }}" alt="Google">
                                </div>
                                <p>Log in with Google</p>
                            </a>
                            <a href="{{ route('social.redirect', ['provider' => 'facebook']) }}"
                                class="ark__social-btn ark__facebook">
                                <div class="ark__icon-wrap">
                                    <img src="{{ asset('assets/images/Facebook.svg') }}" alt="Facebook">
                                </div>
                                <p>Log in with Facebook</p>
                            </a>
                        </div>

                        {{-- Divider (optional)
                    <div class="ark__divider ark__login-width"><span>Or</span></div> --}}

                        {{-- Laravel Login Form --}}
                        <form method="POST" action="{{ route('login') }}" class="ark__login-width">
                            @csrf

                            {{-- Email --}}
                            <div class="ark__form-group">
                                <label class="ark__label" for="email">Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="ark__input" placeholder="you@example.com" required autofocus />
                                @error('email')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Password --}}
                            <div class="ark__form-group">
                                <label class="ark__label" for="password">Password</label>
                                <input type="password" name="password" id="password" class="ark__input"
                                    placeholder="Enter your password" required />
                                @error('password')
                                    <span class="text-red-500 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Remember Me + Forgot Password --}}
                            <div class="ark__checkbox-wrap">
                                <div class="ark__checkbox-group">
                                    <input type="checkbox" name="remember" id="remember" class="ark__checkbox">
                                    <label for="remember">Remember Me</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="ark__forgot-password">Forgot Password?</a>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" class="ark__submit-btn">Log In</button>

                            {{-- Register Link --}}
                            <p class="ark__login-link">
                                Don’t have an account?
                                <a href="{{ route('register') }}">Sign Up</a>
                            </p>
                        </form>
                    </div>

                    {{-- Background Circles --}}
                    <div class="ark__round-one"></div>
                    <div class="ark__round-two"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
