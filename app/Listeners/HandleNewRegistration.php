<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Contracts\Queue\ShouldQueue;

final readonly class HandleNewRegistration implements ShouldQueue
{
    public string $connection;

    public function __construct()
    {
        $this->connection = 'redis';
    }

    public function handle(UserRegistered $event): void
    {
        dispatch(new SendWelcomeEmailJob($event->user->email));
    }
}
