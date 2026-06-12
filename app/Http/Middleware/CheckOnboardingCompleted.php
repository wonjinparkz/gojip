<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOnboardingCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            // Check if onboarding is incomplete OR if user has no branches (data was deleted)
            $needsOnboarding = !$user->onboarding_completed || $user->branches()->count() === 0;

            if ($needsOnboarding) {
                // Skip redirect if already on onboarding page
                if (!$request->is('onboarding')) {
                    // Reset onboarding status if branches were deleted
                    if ($user->onboarding_completed && $user->branches()->count() === 0) {
                        $user->update(['onboarding_completed' => false]);
                    }

                    return redirect()->route('onboarding')
                        ->with('onboarding_required', true)
                        ->with('message', '대시보드를 사용하시려면 먼저 온보딩을 완료해주세요.');
                }
            }
        }

        return $next($request);
    }
}
