<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Site;
use App\Notifications\SslCertExpiringNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

final class CheckSslJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private Site $site) {}

    public function handle(): void
    {
        $parsedHost = parse_url($this->site->url, PHP_URL_HOST);

        if (!is_string($parsedHost)) {
            Log::warning('SSL check skipped: invalid URL', ['site_id' => $this->site->id]);

            return;
        }

        $host = $parsedHost;

        try {
            $result = $this->fetchCertificate($host);

            $this->site->update([
                'ssl_valid' => $result['valid'],
                'ssl_expires_at' => $result['expires_at'],
                'ssl_issuer' => $result['issuer'],
            ]);

            Redis::setex(
                sprintf('site:%s:ssl', $this->site->id),
                86400,
                json_encode([
                    'valid' => $result['valid'],
                    'expires_at' => $result['expires_at']?->toIso8601String(),
                    'issuer' => $result['issuer'],
                ])
            );

            if ($result['valid'] && $result['expires_at'] !== null) {
                $this->checkExpiryThreshold($result['expires_at']);
            }

            Log::info('SSL certificate checked', [
                'site_id' => $this->site->id,
                'host' => $host,
                'valid' => $result['valid'],
                'expires_at' => $result['expires_at']?->toIso8601String(),
                'issuer' => $result['issuer'],
            ]);

        } catch (Exception $exception) {
            $this->handleError($exception, $host);
        }
    }

    /**
     * @return array{valid: bool, expires_at: \Illuminate\Support\Carbon|null, issuer: string|null}
     */
    private function fetchCertificate(string $host): array
    {
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $socket = @stream_socket_client(
            sprintf('ssl://%s:443', $host),
            $errno,
            $errstr,
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if ($socket === false) {
            Log::warning('SSL connection failed', [
                'site_id' => $this->site->id,
                'host' => $host,
                'error' => $errstr,
            ]);

            return ['valid' => false, 'expires_at' => null, 'issuer' => null];
        }

        $params = stream_context_get_params($socket);
        fclose($socket);

        $sslOptions = $params['options']['ssl'] ?? null;
        $peerCert = null;

        if (is_array($sslOptions) && isset($sslOptions['peer_certificate'])) {
            $candidate = $sslOptions['peer_certificate'];
            if ($candidate instanceof \OpenSSLCertificate || is_string($candidate)) {
                $peerCert = $candidate;
            }
        }

        if ($peerCert === null) {
            return ['valid' => false, 'expires_at' => null, 'issuer' => null];
        }

        $cert = openssl_x509_parse($peerCert);

        if ($cert === false) {
            return ['valid' => false, 'expires_at' => null, 'issuer' => null];
        }

        /** @var array{validTo_time_t?: int, issuer?: array{O?: string, CN?: string}} $cert */

        $validToTime = (int) ($cert['validTo_time_t'] ?? 0);

        return [
            'valid' => $validToTime > time(),
            'expires_at' => $validToTime > 0 ? now()->setTimestamp($validToTime) : null,
            'issuer' => (string) ($cert['issuer']['O'] ?? $cert['issuer']['CN'] ?? 'Unknown'),
        ];
    }

    private function checkExpiryThreshold(\Illuminate\Support\Carbon $expiresAt): void
    {
        $daysUntilExpiry = (int) now()->diffInDays($expiresAt, false);

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

    private function handleError(Exception $exception, string $host): void
    {
        $this->site->update([
            'ssl_valid' => false,
            'ssl_expires_at' => null,
            'ssl_issuer' => null,
        ]);

        Log::error('SSL check failed', [
            'site_id' => $this->site->id,
            'host' => $host,
            'error' => $exception->getMessage(),
        ]);
    }
}
