<?php

declare(strict_types=1);

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard;
use App\Livewire\PublicStatus;
use App\Livewire\PublicStatusShow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => ['web']]);

Route::get('/', function (): RedirectResponse {
    if (Auth::check()) {
        return to_route('dashboard');
    }

    return to_route('public.status');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/status', PublicStatus::class)->name('public.status');
    Route::get('/status/{site}', PublicStatusShow::class)->name('public.status.show');
});

Route::prefix('auth')
    ->middleware(['guest'])
    ->group(function (): void {
        Route::get('/register', Register::class)->name('register');
        Route::get('/login', Login::class)->name('login');
    });
Route::middleware(['auth'])->group(function (): void {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});

// Admin routes are handled by Filament at /admin
// Visit /admin/users or /admin/sites directly, or /admin will redirect to login
