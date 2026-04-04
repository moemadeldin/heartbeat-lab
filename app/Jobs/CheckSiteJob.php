<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Events\SiteStatusChanged;
use App\Models\Site;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

final class CheckSiteJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private Site $site) {}

    public function handle(HttpFactory $http): void
    {
        try {
            $startTime = microtime(true);

            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Heartbeat-Lab/1.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
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

            $this->calculateAndUpdateUptime($isOnline);

            Log::info('Site checked', [
                'site_id' => $this->site->id,
                'url' => $this->site->url,
                'status_code' => $response->status(),
                'is_online' => $isOnline,
                'response_time' => $responseTime,
            ]);

            $this->dispatchStatusChangedEvent($isOnline, $response->status(), $responseTime);

        } catch (Exception $exception) {
            $this->site->update([
                'is_online' => false,
                'status_code' => null,
                'response_time' => null,
                'last_checked_at' => now(),
            ]);

            $this->calculateAndUpdateUptime(false);

            Log::error('Site check failed', [
                'site_id' => $this->site->id,
                'url' => $this->site->url,
                'error' => $exception->getMessage(),
            ]);

            $this->dispatchStatusChangedEvent(false, null, null);
        }
    }

    private function dispatchStatusChangedEvent(bool $isOnline, ?int $statusCode, ?float $responseTime): void
    {
        event(new SiteStatusChanged($this->site, $isOnline, $statusCode, $responseTime));
    }

    private function calculateAndUpdateUptime(bool $isOnline): void
    {
        $key = sprintf('site:%s:checks', $this->site->id);

        Redis::rpush($key, $isOnline ? 1 : 0);
        Redis::ltrim($key, -100, -1);

        /** @var array<string|int> $checks */
        $checks = Redis::lrange($key, 0, -1);
        $total = count($checks);

        if ($total === 0) {
            return;
        }

        $onlineCount = array_sum(array_map(fn ($value): int => (int) $value, $checks));

        $uptime = round(($onlineCount / $total) * 100, 2);

        $this->site->update(['uptime' => $uptime]);

        Log::info('Uptime calculated', [
            'site_id' => $this->site->id,
            'uptime' => $uptime,
            'total_checks' => $total,
            'online_checks' => $onlineCount,
        ]);
    }
}
