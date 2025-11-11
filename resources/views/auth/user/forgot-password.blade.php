@extends('auth.user.base')

@section('title', 'Forgot Password')

@section('content')
<!-- Google Fonts: Inter & DM Sans -->
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/forgot-password.css') }}">
<div class="ark__container">
    {{-- Optional Left Image --}}
    {{-- <div class="ark__image-section">
        <div class="ark__image-overlay"></div>
    </div> --}}

    <div class="ark__form-section">
        <div class="ark__form--wrap">
            <div class="ark__form-bg">
                <div class="ark__form--center">
                    
                    {{-- Header --}}
                    <div class="ark__logo ark__login-width">Forgot <span>Password</span></div>
                    <div class="ark__auth-header ark__login-width">
                        <h1 class="ark__title">Reset Your Password</h1>
                        <p class="ark__subtitle">
                            Enter your registered email address and we’ll send you instructions to reset your password.
                        </p>
                    </div>

                    {{-- Status Message --}}
                    @if (session('status'))
                        <div class="ark__login-width mb-4 text-green-600 text-sm text-center">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Forgot Password Form --}}
                    <form method="POST" action="{{ route('password.email') }}" class="ark__login-width" id="forgotPasswordForm">
                        @csrf

                        {{-- Email Field --}}
                        <div class="ark__form-group">
                            <label class="ark__label" for="email">Email</label>
                            <input type="email" name="email" id="email"
                                   class="ark__input"
                                   placeholder="you@example.com"
                                   value="{{ old('email') }}"
                                   required autofocus />
                            @error('email')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" class="ark__submit-btn" id="submitBtn">
                            <span class="btn-text">Send Reset Link</span>
                            <span class="btn-loading" style="display: none;">Sending...</span>
                        </button>

                        {{-- Back to Login --}}
                        <p class="ark__login-link">
                            Remembered your password?
                            <a href="{{ route('login') }}">Log In</a>
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
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotPasswordForm');
    const btn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function(e) {
        const btnText = btn.querySelector('.btn-text');
        const btnLoading = btn.querySelector('.btn-loading');
        
        btn.disabled = true;
        btnText.style.display = 'none';
        btnLoading.style.display = 'inline';
        btn.style.cursor = 'not-allowed';
        btn.style.opacity = '0.7';
    });
});
</script>
@endsection