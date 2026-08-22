<?php

declare(strict_types=1);

use App\Jobs\SendWelcomeEmailJob;
use App\Mail\WelcomeEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
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

it('sends welcome email', function (): void {
    Mail::fake();

    $job = new SendWelcomeEmailJob('test@example.com', 'Test');
    $job->handle();

    Mail::assertSent(WelcomeEmail::class, fn (WelcomeEmail $mail): bool => $mail->hasTo('test@example.com'));
});

it('stores email in public property', function (): void {
    $job = new SendWelcomeEmailJob('john@example.com');

    expect($job->email)->toBe('john@example.com');
});
