<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            // Store intended URL for redirect after login
            if (!$request->expectsJson()) {
                session(['url.intended' => $request->fullUrl()]);
                return redirect()->route('admin.login')
                    ->with('error', 'Please login to access admin panel.');
            }
            return redirect()->route('admin.login');
        }

        if (!Auth::user()->isAdmin()) {
            Auth::logout();
            session()->forget('url.intended');
            return redirect()->route('admin.login')
                ->with('error', 'You do not have permission to access the admin panel.');
        }

        // Additional check for session user_type
        if (session('user_type') !== 'admin') {
            Auth::logout();
            session()->forget('url.intended');
            return redirect()->route('admin.login')
                ->with('error', 'Invalid session. Please login again.');
        }

        return $next($request);
    }
}