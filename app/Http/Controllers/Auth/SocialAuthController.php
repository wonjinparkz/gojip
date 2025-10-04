<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect to social provider
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle social provider callback
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            // Find or create user
            $user = User::where('email', $socialUser->getEmail())
                ->orWhere(function ($query) use ($provider, $socialUser) {
                    $query->where('provider', $provider)
                          ->where('provider_id', $socialUser->getId());
                })
                ->first();

            if (!$user) {
                // Create new user with team
                $user = DB::transaction(function () use ($socialUser, $provider) {
                    $newUser = User::create([
                        'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                        'email' => $socialUser->getEmail() ?? $provider . '_' . $socialUser->getId() . '@example.com',
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                        'email_verified_at' => now(),
                        'password' => Hash::make(Str::random(24)), // Random password
                    ]);

                    // Create personal team
                    $newUser->ownedTeams()->save(Team::forceCreate([
                        'user_id' => $newUser->id,
                        'name' => explode(' ', $newUser->name, 2)[0]."'s Team",
                        'personal_team' => true,
                    ]));

                    return $newUser;
                });
            } else {
                // Update provider info if user exists but didn't have it
                if (!$user->provider || !$user->provider_id) {
                    $user->update([
                        'provider' => $provider,
                        'provider_id' => $socialUser->getId(),
                    ]);
                }
            }

            // Log the user in
            Auth::login($user, true);

            // Redirect to onboarding if not completed, otherwise to dashboard
            if (!$user->onboarding_completed) {
                return redirect()->route('onboarding');
            }

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', '소셜 로그인 중 오류가 발생했습니다.');
        }
    }
}
