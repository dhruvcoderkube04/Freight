<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /* ---------------------- Registration ---------------------- */
    public function showRegistrationForm()
    {
        return view('auth.user.register'); // Updated view path for better separation
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 'email',
            'user_type' => 'user', // Always create as regular user
            'auto_approved' => false,
        ]);

        Auth::login($user);
        // Set session flag for front-end user
        session(['user_type' => 'frontend']);

        return redirect()->intended('/quotes/index');
    }

    /* ---------------------- Login / Logout ---------------------- */
    public function showLoginForm()
    {
        // If already logged in as regular user, redirect to quotes
        if (Auth::check() && Auth::user()->isUser()) {
            return redirect()->route('quotes.index');
        }

        // If admin tries to access front-end login, redirect them to admin login
        if (Auth::check() && Auth::user()->isAdmin()) {
            Auth::logout();
            return redirect()->route('admin.login')
                ->with('error', 'Admin users must login through the admin panel.');
        }

        return view('auth.user.login'); // Updated view path for better separation
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            // Reject admin users - they must use admin login
            if ($user->isAdmin()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Admin users must login through the admin panel at /admin/login',
                ])->onlyInput('email');
            }

            if ($user->type !== 'email') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account uses social login. Please use Google or Facebook to login.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Set session flag for front-end user
            session(['user_type' => 'frontend']);

            return redirect()->intended('/quotes/index');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear front-end session flags
        session()->forget('user_type');
        session()->forget('url.intended');

        return redirect('/login');
    }

    /* ---------------------- Forgot Password ---------------------- */
    public function showForgotForm()
    {
        return view('auth.user.forgot-password'); // Updated view path
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Reject admin users
        if ($user && $user->isAdmin()) {
            return back()->withErrors([
                'email' => 'Admin users must reset password through the admin panel.'
            ]);
        }

        if ($user && $user->type !== 'email') {
            return back()->withErrors([
                'email' => 'This account uses social login. Please use Google or Facebook to login.'
            ]);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /* ---------------------- Reset Password ---------------------- */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.user.reset-password', [ // Updated view path
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /* ---------------------- Social Login ---------------------- */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'fullname' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                    'type' => $provider,
                    'user_type' => 'user',
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                    'auto_approved' => false,
                ]);
            } else {
                $user->update([
                    'type' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            Auth::login($user, true);

            // Reject admin users even from social login
            if ($user->isAdmin()) {
                Auth::logout();
                return redirect('/login')->with('error', 'Admin users must login through the admin panel at /admin/login');
            }

            // Set session flag for front-end user
            session(['user_type' => 'frontend']);

            return redirect()->intended('/quotes/index');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Social login failed! Please try again.');
        }
    }
}