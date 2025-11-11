<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if (Auth::check() && Auth::user()->isUser()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Regular users must login from front-end.');
        }

        return view('auth.admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $user = Auth::user();

            if (!$user->isAdmin()) {
                Auth::logout();
                return back()->withErrors(['email' => 'You do not have admin access.']);
            }

            if ($user->type !== 'email') {
                Auth::logout();
                return back()->withErrors(['email' => 'Admin must use email/password login.']);
            }

            $request->session()->regenerate();
            session(['user_type' => 'admin']);

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session()->forget(['user_type', 'url.intended']);

        return redirect()->route('admin.login');
    }


    // Show Forgot Password Form
    public function showForgotPasswordForm()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.admin.forgot-password');
    }

    // Send Reset Link Email
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Block non-admin or social login admins
        if (!$user || !$user->isAdmin()) {
            return back()->withErrors(['email' => 'No admin account found with that email.']);
        }

        if ($user->type !== 'email') {
            return back()->withErrors(['email' => 'This admin account uses social login. Contact support.']);
        }

        // Use default broker or create custom if needed
        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    // Show Reset Password Form
    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.admin.reset-password')->with([
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // Handle Password Reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('status', trans($status))
            : back()->withErrors(['email' => [trans($status)]]);
    }
}