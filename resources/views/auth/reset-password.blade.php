@extends('auth.base')

@section('title', 'Reset Password')

@section('content')
<!-- Google Fonts: Inter & DM Sans -->
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/forgot-password.css') }}">
<div class="ark__container">
    <!-- Right: Reset Password Form -->
    <div class="ark__form-section ark__reset-password">
        <div class="ark__form--wrap">
            <div class="ark__form-bg">
                <div class="ark__form--center">
                    <div class="ark__auth-header ark__login-width">
                        <h1 class="ark__title">Reset Your Password</h1>
                        <p class="ark__subtitle">Enter your new password below to regain access.</p>
                    </div>

                    @if (session('status'))
                        <div class="mb-4 text-green-600 text-sm text-center">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="ark__login-width" method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                        <div class="ark__form-group">
                            <label class="ark__label">New Password</label>
                            <input type="password" name="password" id="password" class="ark__input" placeholder="Enter Your Password" required autofocus>
                            @error('password')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="ark__form-group">
                            <label class="ark__label">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="ark__input" placeholder="Re-Enter Password For Safety" required>
                        </div>

                        <button type="submit" class="ark__submit-btn">Reset Password</button>

                        <p class="ark__login-link">
                            <a href="{{ route('login') }}">Back to Login</a>
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
