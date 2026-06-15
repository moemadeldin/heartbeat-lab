<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\SiteStatusChanged;
use App\Models\Site;
use App\Utilities\HttpDefaults;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

final class CheckSiteJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private Site $site) {}

    public function handle(): void
    {
        try {
            $previousOnline = $this->site->is_online ?? false;

            $startTime = microtime(true);

            $response = Http::timeout(HttpDefaults::HTTP_TIMEOUT)
                ->connectTimeout(HttpDefaults::CONNECT_TIMEOUT)
                ->retry(HttpDefaults::RETRY_TIMES, HttpDefaults::RETRY_DELAY, throw: false)
                ->withHeaders([
                    'User-Agent' => HttpDefaults::USER_AGENT,
                    'Accept' => HttpDefaults::ACCEPT,
                ])
                ->get($this->site->url);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            $isOnline = $response->successful() && $response->status() === 200;

            $this->site->update([
                'is_online' => $isOnline,
                'status_code' => $response->status(),
                'response_time' => $responseTime,
                'last_checked_at' => now(),
            ]);

            $uptime = $this->calculateAndUpdateUptime($isOnline);

            Redis::setex(
                sprintf('site:%s:status', $this->site->id),
                120,
                json_encode([
                    'is_online' => $isOnline,
                    'uptime' => $uptime,
                    'status_code' => $response->status(),
                    'response_time' => $responseTime,
                    'last_checked_at' => now()->toIso8601String(),
                ])
            );

            Log::info('Site checked', [
                'site_id' => $this->site->id,
                'url' => $this->site->url,
                'status_code' => $response->status(),
                'is_online' => $isOnline,
                'response_time' => $responseTime,
            ]);

            $this->dispatchStatusChangedEvent($isOnline, $response->status(), $responseTime, $previousOnline);

        } catch (Exception $exception) {
            $previousOnline = $this->site->is_online ?? false;
            $this->site->update([
                'is_online' => false,
                'status_code' => null,
                'response_time' => null,
                'last_checked_at' => now(),
            ]);

            $uptime = $this->calculateAndUpdateUptime(false);

            Redis::setex(
                sprintf('site:%s:status', $this->site->id),
                120,
                json_encode([
                    'is_online' => false,
                    'uptime' => $uptime,
                    'status_code' => null,
                    'response_time' => null,
                    'last_checked_at' => now()->toIso8601String(),
                ])
            );

            Log::error('Site check failed', [
                'site_id' => $this->site->id,
                'url' => $this->site->url,
                'error' => $exception->getMessage(),
            ]);

            $this->dispatchStatusChangedEvent(false, null, null, $previousOnline);
        }
    }

    private function dispatchStatusChangedEvent(bool $isOnline, ?int $statusCode, ?float $responseTime, bool $previousOnline = false): void
    {
        event(new SiteStatusChanged($this->site, $isOnline, $statusCode, $responseTime, $previousOnline));
    }

    private function calculateAndUpdateUptime(bool $isOnline): float
    {
        $key = sprintf('site:%s:checks', $this->site->id);

        Redis::rpush($key, $isOnline ? 1 : 0);
        Redis::ltrim($key, -100, -1);

        /** @var array<string|int> $checks */
        $checks = Redis::lrange($key, 0, -1);
        $total = count($checks);

        $uptime = 0.00;

        if ($total > 0) {
            $onlineCount = array_sum(array_map(fn (string $value): int => (int) $value, $checks));

            $uptime = round(($onlineCount / $total) * 100, 2);

            $this->site->update(['uptime' => $uptime]);

            Log::info('Uptime calculated', [
                'site_id' => $this->site->id,
                'uptime' => $uptime,
                'total_checks' => $total,
                'online_checks' => $onlineCount,
            ]);
        }

        return $uptime;
    }
}
