<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Support\Carbon;
use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class SslCertExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Site $site,
        private int $daysUntilExpiry,
        private Carbon $expiresAt,
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
        $subject = $this->daysUntilExpiry <= 0
            ? '[Heartbeat Lab] 🔴 SSL certificate EXPIRED for ' . $this->site->name
            : '[Heartbeat Lab] 🟡 SSL certificate expiring for ' . $this->site->name;

        return (new MailMessage)
            ->subject($subject)
            ->markdown('emails.ssl-expiry', [
                'site' => $this->site,
                'daysUntilExpiry' => $this->daysUntilExpiry,
                'expiresAt' => $this->expiresAt,
                'dashboardUrl' => url('/dashboard'),
            ]);
    }
}
