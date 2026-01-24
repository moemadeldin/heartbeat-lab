<?php

declare(strict_types=1);

use App\Events\UserRegistered;
use App\Jobs\SendWelcomeEmailJob;
use App\Listeners\HandleNewRegistration;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Queue;

it('dispatches welcome email job when handling event', function (): void {
    Queue::fake();

    $user = User::factory()->create(['email' => 'test@example.com']);
    $event = new UserRegistered($user);

    $listener = new HandleNewRegistration();
    $listener->handle($event);

    Queue::assertPushed(SendWelcomeEmailJob::class, fn ($job): bool => $job->email === 'test@example.com');
});

it('implements should queue interface', function (): void {
    $listener = new HandleNewRegistration();

    expect($listener)->toBeInstanceOf(ShouldQueue::class);
});

it('uses redis connection', function (): void {
    $listener = new HandleNewRegistration();

    expect($listener->connection)->toBe('redis');
});

it('handles multiple registrations', function (): void {
    Queue::fake();

    $users = User::factory()->count(3)->create();
    $listener = new HandleNewRegistration();

    foreach ($users as $user) {
        $event = new UserRegistered($user);
        $listener->handle($event);
    }

    Queue::assertPushed(SendWelcomeEmailJob::class, 3);
});
