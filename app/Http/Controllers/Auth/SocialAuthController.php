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

            // Check if user has branches and rooms
            $hasBranches = $user->branches()->exists();
            $hasRooms = $user->branches()->whereHas('rooms')->exists();

            // If user has branches and rooms, mark onboarding as completed
            if ($hasBranches && $hasRooms) {
                if (!$user->onboarding_completed) {
                    $user->update(['onboarding_completed' => true]);
                }

                // Set current branch in session if not set
                if (!session()->has('current_branch_id')) {
                    $firstBranch = $user->branches()->first();
                    if ($firstBranch) {
                        session(['current_branch_id' => $firstBranch->id]);
                    }
                }

                return redirect('/admin');
            }

            // If no branches or rooms exist, reset onboarding status and redirect to onboarding
            if ($user->onboarding_completed) {
                $user->update(['onboarding_completed' => false]);
            }

            return redirect()->route('onboarding');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', '소셜 로그인 중 오류가 발생했습니다.');
        }
    }
}
