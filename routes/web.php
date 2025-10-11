<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Social Login Routes
Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirectToProvider'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])->name('social.callback');

// Onboarding Route (authenticated users only)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
])->group(function () {
    Route::get('/onboarding', \App\Livewire\Onboarding\OnboardingWizard::class)->name('onboarding');
});

// Protected Routes (require authentication and completed onboarding)
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    \App\Http\Middleware\CheckOnboardingCompleted::class,
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Tenant calendar update endpoint
    Route::post('/admin/tenants/{tenant}/update-dates', function (\App\Models\Tenant $tenant) {
        $validated = request()->validate([
            'move_in_date' => 'nullable|date',
            'move_out_date' => 'nullable|date',
        ]);

        $tenant->update($validated);

        return response()->json(['success' => true]);
    })->name('tenants.update-dates');

    // Branch switching endpoint
    Route::get('/admin/switch-branch/{branch}', function (\App\Models\Branch $branch) {
        $user = auth()->user();

        if ($user->branches->contains($branch)) {
            session(['current_branch_id' => $branch->id]);
        }

        return redirect()->back();
    })->name('filament.admin.pages.switch-branch');

    // Branch add endpoint
    Route::post('/admin/add-branch', function () {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        $branch = auth()->user()->branches()->create([
            'name' => $validated['name'],
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'start_floor' => 1,
            'end_floor' => 1,
        ]);

        // Set as current branch
        session(['current_branch_id' => $branch->id]);

        return response()->json(['success' => true, 'branch' => $branch]);
    })->name('filament.admin.pages.add-branch');

    // Tenant quick create endpoint (for modal)
    Route::post('/admin/tenants/quick-create', function () {
        $validated = request()->validate([
            'name' => 'required|string|max:255',
            'room_id' => 'required|exists:rooms,id',
            'move_in_date' => 'required|date',
            'move_out_date' => 'nullable|date',
        ]);

        $room = \App\Models\Room::findOrFail($validated['room_id']);

        $tenant = \App\Models\Tenant::create([
            'name' => $validated['name'],
            'branch_id' => $room->branch_id,
            'room_id' => $validated['room_id'],
            'room_number' => $room->room_number,
            'move_in_date' => $validated['move_in_date'],
            'move_out_date' => $validated['move_out_date'],
            'payment_status' => 'pending',
        ]);

        // 방 상태 업데이트
        $room->update([
            'status' => 'occupied',
            'tenant_name' => $validated['name'],
            'move_in_date' => $validated['move_in_date'],
            'move_out_date' => $validated['move_out_date'],
        ]);

        return response()->json([
            'success' => true,
            'tenant' => [
                'id' => $tenant->id,
                'room_number' => $tenant->room_number,
                'payment_status_color' => match($tenant->payment_status) {
                    'paid' => '#10b981',
                    'overdue' => '#ef4444',
                    'pending' => '#f59e0b',
                    default => '#6b7280',
                },
            ]
        ]);
    })->name('tenants.quick-create');
});
