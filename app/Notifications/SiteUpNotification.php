<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class SiteUpNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Site $site,
        private int $statusCode,
        private float $responseTime,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[Heartbeat Lab] \u{1F7E2} {$this->site->name} is back ONLINE")
            ->markdown('emails.site-status', [
                'site' => $this->site,
                'isOnline' => true,
                'statusText' => "HTTP {$this->statusCode}",
                'responseTime' => $this->responseTime,
                'checkedAt' => $this->site->last_checked_at,
                'dashboardUrl' => url('/dashboard'),
            ]);
    }
}
