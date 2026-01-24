<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Events\UserRegistered;
use App\Jobs\SendWelcomeEmailJob;
use App\Listeners\HandleNewRegistration;
use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

it('renders the registration page', function (): void {
    $this->get('/auth/register')->assertStatus(Response::HTTP_OK);
});

it('can register a new user', function (): void {
    Event::fake();

    Livewire::test(Register::class)
        ->set('name', 'John')
        ->set('email', 'john@gmail.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user = User::query()->where('email', 'john@gmail.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('John');
    Event::assertDispatched(UserRegistered::class);

    expect(auth()->check())->toBeTrue();
    expect(auth()->id())->toBe($user->id);

});
it('dispatches welcome email job when user registers', function (): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'test@gmail.com']);
    $event = new UserRegistered($user);

    $listener = new HandleNewRegistration();
    $listener->handle($event);

    Queue::assertPushed(SendWelcomeEmailJob::class, fn ($job): bool => $job->email === 'test@gmail.com');
});
it('requires all fields', function ($field, $value): void {
    Livewire::test(Register::class)
        ->set($field, $value)
        ->call('register')
        ->assertHasErrors([$field => 'required']);
})->with([
    ['name', ''],
    ['email', ''],
    ['password', ''],
]);

it('cannot register a new user with fake email', function (): void {
    Livewire::test(Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['email']);

    $this->assertDatabaseMissing('users', ['email' => 'john@example.com']);
    expect(auth()->check())->toBeFalse();
});

it('cannot register a new user with password less than 8 chars', function (): void {
    Livewire::test(Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@gmail.com')
        ->set('password', 'passwo')
        ->set('password_confirmation', 'passwo')
        ->call('register')
        ->assertHasErrors(['password']);

    $this->assertDatabaseMissing('users', ['email' => 'john@gmail.com']);
    expect(auth()->check())->toBeFalse();
});

it('cannot register a new user with wrong password confirmation', function (): void {
    Livewire::test(Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@gmail.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password12223')
        ->call('register')
        ->assertHasErrors(['password']);

    $this->assertDatabaseMissing('users', ['email' => 'john@gmail.com']);
    expect(auth()->check())->toBeFalse();
});
