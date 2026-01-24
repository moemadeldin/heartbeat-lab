<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class SendWelcomeEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $email) {}

    public function handle(): void
    {
        Log::info('Redis Queue: Processing welcome email for '.$this->email);
    }
}
