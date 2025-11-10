<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    protected $timeout = 3600; // 1 hour

    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $lastActivity = session('lastActivityTime');
            $userType = session('user_type');

            if ($lastActivity && time() - $lastActivity > $this->timeout) {
                $isAdminRoute = $request->is('admin*');

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redirect based on route or user type
                if ($isAdminRoute || $userType === 'admin') {
                    return redirect()->route('admin.login')->with('error', 'You have been logged out due to inactivity.');
                }

                return redirect()->route('login')->with('error', 'You have been logged out due to inactivity.');
            }

            session(['lastActivityTime' => time()]);
        }

        return $next($request);
    }
}