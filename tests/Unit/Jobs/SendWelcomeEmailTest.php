<?php

declare(strict_types=1);

use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

it('can be dispatched to queue', function (): void {
    Queue::fake();

    dispatch(new SendWelcomeEmailJob('test@example.com'));

    Queue::assertPushed(SendWelcomeEmailJob::class, fn ($job): bool => $job->email === 'test@example.com');
});

it('implements should queue interface', function (): void {
    $job = new SendWelcomeEmailJob('test@example.com');

    expect($job)->toBeInstanceOf(ShouldQueue::class);
});

it('logs welcome email processing', function (): void {
    Log::shouldReceive('info')
        ->once()
        ->with('Redis Queue: Processing welcome email for test@example.com');

    $job = new SendWelcomeEmailJob('test@example.com');
    $job->handle();
});

it('stores email in public property', function (): void {
    $job = new SendWelcomeEmailJob('john@example.com');

    expect($job->email)->toBe('john@example.com');
});
