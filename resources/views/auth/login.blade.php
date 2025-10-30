@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input type="password" name="password" id="password" 
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-blue-500">
                @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4 flex items-center">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-sm text-gray-700">Remember me</label>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                Login
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-gray-600">Or login with:</p>
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
            <a href="{{ route('register') }}" class="text-blue-500 hover:underline">
                Don't have an account? Register
            </a>
        </div>
    </div>
</div>
@endsection