@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="fullname">
                    Full Name
                </label>
                <input type="text" name="fullname" id="fullname" value="{{ old('fullname') }}" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                @error('fullname') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="user_type">
                    User Type
                </label>
                <select name="user_type" id="user_type" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="individual" {{ old('user_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                    <option value="business" {{ old('user_type') == 'business' ? 'selected' : '' }}>Business</option>
                    <option value="corporate" {{ old('user_type') == 'corporate' ? 'selected' : '' }}>Corporate</option>
                </select>
                @error('user_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input type="password" name="password" id="password" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                    Confirm Password
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                Register
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">Or register with:</p>
            <div class="mt-4 flex justify-center space-x-4">
                <a href="{{ route('social.login', 'google') }}" 
                   class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                    Google
                </a>
                <a href="{{ route('social.login', 'facebook') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Facebook
                </a>
            </div>
        </div>

        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-blue-500 hover:underline">
                Already have an account? Login
            </a>
        </div>
    </div>
</div>
@endsection