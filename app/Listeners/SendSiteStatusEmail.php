<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\SiteStatusChanged;
use App\Notifications\SiteDownNotification;
use App\Notifications\SiteUpNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Response;

final class SendSiteStatusEmail implements ShouldQueue
{
    public function handle(SiteStatusChanged $event): void
    {
        if ($event->previousOnline === $event->isOnline) {
            return;
        }

        $user = $event->site->user;

        if ($user === null) {
            return;
        }

        if ($event->isOnline) {
            $user->notify(new SiteUpNotification(
                site: $event->site,
                statusCode: $event->statusCode ?? Response::HTTP_OK,
                responseTime: $event->responseTime ?? 0.0,
            ));
        } else {
            $user->notify(new SiteDownNotification(
                site: $event->site,
                statusCode: $event->statusCode,
                responseTime: $event->responseTime,
            ));
        }
    }
}
