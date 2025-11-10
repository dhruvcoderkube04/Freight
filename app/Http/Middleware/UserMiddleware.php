<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     * Ensure only regular users can access front-end routes
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Store intended URL for redirect after login
            if (!$request->expectsJson()) {
                session(['url.intended' => $request->fullUrl()]);
                return redirect()->route('login')
                    ->with('error', 'Please login to access this page.');
            }
            return redirect()->route('login');
        }

        // Reject admin users from front-end routes
        if (Auth::user()->isAdmin()) {
            Auth::logout();
            session()->forget('url.intended');
            return redirect()->route('admin.login')
                ->with('error', 'Admin users must access the admin panel.');
        }

        // Ensure user is a regular user
        if (!Auth::user()->isUser()) {
            Auth::logout();
            session()->forget('url.intended');
            return redirect()->route('login')
                ->with('error', 'Access denied.');
        }

        // Additional check for session user_type
        if (session('user_type') !== 'frontend') {
            Auth::logout();
            session()->forget('url.intended');
            return redirect()->route('login')
                ->with('error', 'Invalid session. Please login again.');
        }

        return $next($request);
    }
}