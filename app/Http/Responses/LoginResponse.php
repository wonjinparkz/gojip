<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Redirect to onboarding if not completed, otherwise to dashboard
        if ($request->user() && !$request->user()->onboarding_completed) {
            return redirect()->route('onboarding');
        }

        return redirect()->intended(config('fortify.home'));
    }
}
