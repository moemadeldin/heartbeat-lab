<?php

declare(strict_types=1);

use App\Livewire\Auth\Logout;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders logout button', function (): void {
    Livewire::test(Logout::class)
        ->assertStatus(200)
        ->assertSee('Logout');
});

it('logs out user and redirects to login', function (): void {
    Livewire::test(Logout::class)
        ->call('logout')
        ->assertRedirect(route('login'));

    expect(auth()->check())->toBeFalse();
});

it('destroys the session on logout', function (): void {
    $sessionId = session()->getId();

    Livewire::test(Logout::class)
        ->call('logout');

    expect(auth()->check())->toBeFalse();
    expect(session()->getId())->not->toBe($sessionId);
});

it('can handle logout from multiple sessions', function (): void {
    // Create another session
    $response = $this->post(route('login'), [
        'email' => $this->user->email,
        'password' => 'password',
    ]);

    Livewire::test(Logout::class)
        ->call('logout')
        ->assertRedirect(route('login'));

    expect(auth()->check())->toBeFalse();
});
