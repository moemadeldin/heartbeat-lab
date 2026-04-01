<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Site;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

            Log::info('Site checked', [
                'site_id' => $this->site->id,
                'url' => $this->site->url,
                'status_code' => $response->status(),
                'is_online' => $isOnline,
                'response_time' => $responseTime,
            ]);

        } catch (Exception $exception) {
            $this->site->update([
                'is_online' => false,
                'status_code' => null,
                'response_time' => null,
                'last_checked_at' => now(),
            ]);

            Log::error('Site check failed', [
                'site_id' => $this->site->id,
                'url' => $this->site->url,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
