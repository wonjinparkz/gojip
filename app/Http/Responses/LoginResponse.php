<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->intended('/admin');
        }

        // Check if user has completed onboarding by checking if they have branches and rooms
        $hasBranches = $user->branches()->exists();
        $hasRooms = $user->branches()->whereHas('rooms')->exists();

        // If user has branches and rooms, mark onboarding as completed and redirect to admin
        if ($hasBranches && $hasRooms) {
            if (!$user->onboarding_completed) {
                $user->update(['onboarding_completed' => true]);
            }
            return redirect()->intended('/admin');
        }

        // If onboarding is not completed and no data exists, redirect to onboarding
        if (!$user->onboarding_completed) {
            return redirect()->route('onboarding');
        }

        return redirect()->intended('/admin');
    }
}
