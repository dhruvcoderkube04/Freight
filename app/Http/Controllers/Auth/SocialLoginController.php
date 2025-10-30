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
                    'password' => Hash::make(uniqid()), // Random password
                    'type' => 'user',
                    'user_type' => 'individual',
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                    'auto_approved' => false,
                ]);

                // Store provider token
                if ($provider === 'google') {
                    $user->update(['google_token' => $socialUser->token]);
                } elseif ($provider === 'facebook') {
                    $user->update(['facebook_token' => $socialUser->token]);
                }
            } else {
                // Update existing user with provider info
                $updateData = [
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ];

                // Update provider token
                if ($provider === 'google') {
                    $updateData['google_token'] = $socialUser->token;
                } elseif ($provider === 'facebook') {
                    $updateData['facebook_token'] = $socialUser->token;
                }

                $user->update($updateData);
            }

            Auth::login($user, true);

            return redirect()->intended('/shipments/create');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Social login failed! Please try again.');
        }
    }
}