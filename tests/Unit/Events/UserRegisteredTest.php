<?php

declare(strict_types=1);

use App\Events\UserRegistered;
use App\Models\User;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Support\Facades\Event;

it('dispatches user registered event with correct data', function (): void {
    Event::fake();

    $user = User::factory()->create(['name' => 'John Doe']);

    event(new UserRegistered($user));

    Event::assertDispatched(UserRegistered::class, fn ($event): bool => $event->user->id === $user->id);
});

it('broadcasts on public announcements channel', function (): void {
    $user = User::factory()->create(['name' => 'John Doe']);
    $event = new UserRegistered($user);

    $channels = $event->broadcastOn();

    expect($channels)->toHaveCount(1);
    expect($channels[0]->name)->toBe('public-announcements');
});

it('broadcasts with correct event name', function (): void {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    expect($event->broadcastAs())->toBe('UserRegistered');
});

it('broadcasts with correct data', function (): void {
    $user = User::factory()->create(['name' => 'Jane Smith']);
    $event = new UserRegistered($user);

    $data = $event->broadcastWith();

    expect($data)->toHaveKey('name', 'Jane Smith');
    expect($data)->toHaveKey('message', 'A new lab member has arrived!');
});

it('should broadcast now', function (): void {
    $user = User::factory()->create();
    $event = new UserRegistered($user);

    expect($event)->toBeInstanceOf(ShouldBroadcastNow::class);
});
