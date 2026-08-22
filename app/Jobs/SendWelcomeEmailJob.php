<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\WelcomeEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

final class SendWelcomeEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $email,
        public string $name = '',
    ) {}

    public function handle(): void
    {
        Mail::to($this->email)->send(new WelcomeEmail($this->name));
    }
}
