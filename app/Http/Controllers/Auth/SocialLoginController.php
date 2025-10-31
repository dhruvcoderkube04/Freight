<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            // Check if user already exists
            $user = User::where('email', $socialUser->getEmail())->first();

            if (!$user) {
                // Create new user
                $user = User::create([
                    'fullname' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(uniqid()),
                    'type' => $provider, // 'google' or 'facebook'
                    'user_type' => 'user',
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                    'auto_approved' => false,
                ]);

                // Store provider token
                if ($provider === 'google') {
                    $user->google_token = $socialUser->token;
                } elseif ($provider === 'facebook') {
                    $user->facebook_token = $socialUser->token;
                }
                $user->save();

            } else {
                // Update existing user with provider info
                $user->update([
                    'type' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);

                // Update provider token
                if ($provider === 'google') {
                    $user->google_token = $socialUser->token;
                } elseif ($provider === 'facebook') {
                    $user->facebook_token = $socialUser->token;
                }
                $user->save();
            }

            Auth::login($user, true);

            return redirect()->intended('/shipments/create');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Social login failed! Please try again.');
        }
    }
}