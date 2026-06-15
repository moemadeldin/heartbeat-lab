<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\SslCertificateService;
use App\Utilities\HttpDefaults;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

final class CheckPublicStatusJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly string $url,
        private readonly string $token,
    ) {}

    public function handle(SslCertificateService $ssl): void
    {
        $startTime = microtime(true);

        try {
            $response = Http::timeout(HttpDefaults::HTTP_TIMEOUT)
                ->connectTimeout(HttpDefaults::CONNECT_TIMEOUT)
                ->retry(HttpDefaults::RETRY_TIMES, HttpDefaults::RETRY_DELAY, throw: false)
                ->withHeaders([
                    'User-Agent' => HttpDefaults::USER_AGENT,
                    'Accept' => HttpDefaults::ACCEPT,
                ])
                ->get($this->url);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            $isOnline = $response->successful();
            $statusCode = $response->status();
            $error = null;
        } catch (ConnectionException) {
            $responseTime = null;
            $isOnline = false;
            $statusCode = null;
            $error = 'Could not connect to host.';
        } catch (Exception $exception) {
            $responseTime = null;
            $isOnline = false;
            $statusCode = null;
            $error = $exception->getMessage();
        }

        $sslData = $ssl->check($this->url);

        $result = [
            'is_online' => $isOnline,
            'status_code' => $statusCode,
            'response_time' => $responseTime,
            'error' => $error,
            'ssl_valid' => $sslData['ssl_valid'],
            'ssl_issuer' => $sslData['ssl_issuer'],
            'ssl_days_left' => $sslData['ssl_days_left'],
            'checked_at' => now()->toIso8601String(),
        ];

        Redis::setex(
            sprintf('public-check:%s', $this->token),
            300,
            json_encode($result),
        );

        Log::info('Public status check completed', [
            'url' => $this->url,
            'token' => $this->token,
            'is_online' => $isOnline,
        ]);
    }
}
