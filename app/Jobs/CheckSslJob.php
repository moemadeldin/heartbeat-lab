<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Site;
use App\Notifications\SslCertExpiringNotification;
use App\Services\SslCertificateService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

final class CheckSslJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Site $site,
    ) {}

    public function handle(): void
    {
        $url = $this->site->url;

        try {
            $result = resolve(SslCertificateService::class)->checkWithExpiry($url);

            $this->site->update([
                'ssl_valid' => $result['ssl_valid'],
                'ssl_expires_at' => $result['ssl_expires_at'],
                'ssl_issuer' => $result['ssl_issuer'],
            ]);

            Redis::setex(
                sprintf('site:%s:ssl', $this->site->id),
                86400,
                json_encode([
                    'valid' => $result['ssl_valid'],
                    'expires_at' => $result['ssl_expires_at']?->toIso8601String(),
                    'issuer' => $result['ssl_issuer'],
                ])
            );

            if ($result['ssl_valid'] && $result['ssl_expires_at'] !== null) {
                $this->checkExpiryThreshold($result['ssl_expires_at']);
            }

            Log::info('SSL certificate checked', [
                'site_id' => $this->site->id,
                'url' => $url,
                'valid' => $result['ssl_valid'],
                'expires_at' => $result['ssl_expires_at']?->toIso8601String(),
                'issuer' => $result['ssl_issuer'],
            ]);

        } catch (Exception $exception) {
            $this->handleError($exception, $url);
        }
    }

    private function checkExpiryThreshold(Carbon $expiresAt): void
    {
        $daysUntilExpiry = (int) now()->diffInDays($expiresAt);
        $daysUntilExpiry = $expiresAt->isPast() ? -$daysUntilExpiry : $daysUntilExpiry;

        if ($daysUntilExpiry > 30) {
            return;
        }

        $notifiedKey = sprintf('site:%s:ssl:notified', $this->site->id);
        $recentlyNotified = Redis::get($notifiedKey);

        if ($recentlyNotified !== null) {
            return;
        }

        $this->site->user?->notify(new SslCertExpiringNotification(
            site: $this->site,
            daysUntilExpiry: $daysUntilExpiry,
            expiresAt: $expiresAt,
        ));

        Redis::setex($notifiedKey, 86400, '1');
    }

    private function handleError(Exception $exception, string $url): void
    {
        $this->site->update([
            'ssl_valid' => null,
            'ssl_expires_at' => null,
            'ssl_issuer' => null,
        ]);

        Log::error('SSL check failed', [
            'site_id' => $this->site->id,
            'url' => $url,
            'error' => $exception->getMessage(),
        ]);
    }
}
