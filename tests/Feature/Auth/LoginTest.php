<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Http\Response;
use Livewire\Livewire;

it('renders the login page', function (): void {
    $this->get('/auth/login')->assertStatus(Response::HTTP_OK);
});

it('can login a user', function (): void {

    User::factory()->create([
        'name' => 'john',
        'email' => 'john@gmail.com',
        'password' => 'password123',
    ]);
    Livewire::test(Login::class)
        ->set('email', 'john@gmail.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue();
});

it('requires all fields', function ($field, $value): void {
    Livewire::test(Login::class)
        ->set($field, $value)
        ->call('login')
        ->assertHasErrors([$field => 'required']);
})->with([
    ['email', ''],
    ['password', ''],
]);

it('cannot login a user with fake email', function (): void {

    Livewire::test(Login::class)
        ->set('email', 'john@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors(['email']);

    expect(auth()->check())->toBeFalse();
});

it('cannot login user with wrong password', function (): void {

    User::factory()->create([
        'name' => 'john',
        'email' => 'john@gmail.com',
        'password' => 'password123',
    ]);
    Livewire::test(Login::class)
        ->set('email', 'john@gmail.com')
        ->set('password', 'password12')
        ->call('login')
        ->assertHasErrors(['password']);

    expect(auth()->check())->toBeFalse();
});
