@extends('auth.base')

@section('title', 'Ark Sign Up')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('assets/css/signup.css') }}">
<div class="ark__container">
    <!-- Left: Image -->
    <div class="ark__image-section">
        <div class="ark__image-overlay"></div>
    </div>

    <!-- Right: Sign Up Form -->
    <div class="ark__form-section">
        <div class="ark__logo">Sign<span>Up</span></div>

        <div class="ark__auth-header">
            <h1 class="ark__title">Create An Account</h1>
            <p class="ark__subtitle mb-0">
                Create Your Account To Manage And Track Your Shipments With Ease.
            </p>
        </div>

        <!-- Social Buttons -->
        <div class="ark__social-buttons">
            <a href="{{ route('social.login', 'google') }}" class="ark__social-btn ark__google">
                <div class="ark__icon-wrap">
                    <img src="{{ asset('assets/images/GoogleIcon.svg') }}" alt="Google">
                </div>
                <p class="mb-0">Sign up with Google</p>
            </a>
            <a href="{{ route('social.login', 'facebook') }}" class="ark__social-btn ark__facebook">
                <div class="ark__icon-wrap">
                    <img src="{{ asset('assets/images/Facebook.svg') }}" alt="Facebook">
                </div>
                <p class="mb-0">Sign up with Facebook</p>
            </a>
        </div>

        <!-- Divider -->
        <div class="ark__divider"><span>Or</span></div>

        <!-- Laravel Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="ark__form-group">
                <label class="ark__label" for="fullname">Full Name</label>
                <input type="text" name="fullname" id="fullname" class="ark__input"
                       placeholder="Enter Your Full Name" value="{{ old('fullname') }}" required>
                @error('fullname')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="ark__form-group">
                <label class="ark__label" for="email">Email</label>
                <input type="email" name="email" id="email" class="ark__input"
                       placeholder="you@example.com" value="{{ old('email') }}" required>
                @error('email')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="ark__form-group">
                <label class="ark__label" for="password">Password</label>
                <input type="password" name="password" id="password" class="ark__input"
                       placeholder="Enter Your Password" required>
                @error('password')
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="ark__form-group">
                <label class="ark__label" for="password_confirmation">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="ark__input" placeholder="Re-enter Password" required>
            </div>

            <div class="ark__checkbox-group">
                <input type="checkbox" id="terms" class="ark__checkbox" required>
                <label for="terms">
                    I Agree To The <a href="#">Terms & Conditions</a> And <a href="#">Privacy Policy</a>
                </label>
            </div>

            <button type="submit" class="ark__submit-btn">Sign Up</button>

            <p class="ark__login-link">
                Already Have An Account? <a href="{{ route('login') }}">Log In</a>
            </p>
        </form>
    </div>
</div>
@endsection
