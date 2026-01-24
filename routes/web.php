<?php

declare(strict_types=1);

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Broadcast::routes(['middleware' => ['web']]);

Route::get('/', fn (): Factory|View => view('welcome'));

Route::prefix('auth')
    ->middleware(['guest'])
    ->group(function (): void {
        Route::get('/register', Register::class)->name('register');
        Route::get('/login', Login::class)->name('login');
    });

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
});
