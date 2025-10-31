<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Only allow email-based users to login with password
        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();
            
            // Check if user is email-based user
            if ($user->type !== 'email') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account uses social login. Please use Google or Facebook to login.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            return redirect()->intended('/shipments/create');
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
        return redirect('/login');
    }
}