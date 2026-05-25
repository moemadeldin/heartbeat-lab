<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class SiteDownNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Site $site,
        private ?int $statusCode,
        private ?float $responseTime,
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
        $statusText = $this->statusCode !== null
            ? "HTTP {$this->statusCode}"
            : 'Connection Error';

        return (new MailMessage)
            ->subject("[Heartbeat Lab] \u{1F534} {$this->site->name} is DOWN")
            ->markdown('emails.site-status', [
                'site' => $this->site,
                'isOnline' => false,
                'statusText' => $statusText,
                'responseTime' => $this->responseTime,
                'checkedAt' => $this->site->last_checked_at,
                'dashboardUrl' => url('/dashboard'),
            ]);
    }
}
