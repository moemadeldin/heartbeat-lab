<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SiteStatus;
use App\Events\SiteStatusChanged;
use App\Interfaces\UrlValidator;
use App\Models\Site;
use App\Utilities\HttpDefaults;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

final class CheckSiteJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private Site $site) {}

    public function handle(UrlValidator $validator): void
    {
        $validator->validateForMonitoring($this->site->url);

        $previousStatus = $this->site->status ?? SiteStatus::Checking;
        $wasCheckedBefore = $this->site->checks()->exists();
        $isOnline = false;
        $statusCode = null;
        $responseTime = null;

        try {
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
            $isOnline = $response->successful();
            $statusCode = $response->status();

            Log::info('Site checked', [
                'site_id' => $this->site->id,
                'url' => $this->site->url,
                'status_code' => $statusCode,
                'is_online' => $isOnline,
                'response_time' => $responseTime,
            ]);
        } catch (Exception $exception) {
            Log::error('Site check failed', [
                'site_id' => $this->site->id,
                'url' => $this->site->url,
                'error' => $exception->getMessage(),
            ]);
        }

        $status = $isOnline ? SiteStatus::Online : SiteStatus::Offline;
        $uptime = 0.0;

        DB::transaction(function () use ($status, $statusCode, $responseTime, &$uptime): void {
            $this->site->update([
                'status' => $status,
            ]);

            $this->persistCheckResult($status, $statusCode, $responseTime);
            $uptime = $this->calculateAndUpdateUptime();
        });

        Redis::setex(
            sprintf('site:%s:status', $this->site->id),
            120,
            json_encode([
                'status' => $status->value,
                'uptime' => $uptime,
                'status_code' => $statusCode,
                'response_time' => $responseTime,
                'last_checked_at' => now()->toIso8601String(),
            ]),
        );

        if ($wasCheckedBefore) {
            event(new SiteStatusChanged($this->site, $status, $statusCode, $responseTime, $previousStatus));
        }
    }

    private function persistCheckResult(SiteStatus $status, ?int $statusCode, ?float $responseTime): void
    {
        DB::table('site_checks')->insert([
            'site_id' => $this->site->id,
            'status' => $status->value,
            'status_code' => $statusCode,
            'response_time' => $responseTime,
            'checked_at' => now(),
        ]);

        DB::table('site_checks')
            ->where('site_id', $this->site->id)
            ->where('id', '<', DB::raw("(SELECT id FROM site_checks WHERE site_id = '".$this->site->id."' ORDER BY id DESC LIMIT 1 OFFSET 100)"))
            ->delete();
    }

    private function calculateAndUpdateUptime(): float
    {
        $checks = DB::table('site_checks')
            ->where('site_id', $this->site->id)
            ->orderByDesc('id')
            ->limit(100)
            ->pluck('status');

        $total = $checks->count();

        if ($total === 0) {
            return 0.0;
        }

        $onlineCount = $checks->filter(fn ($s): bool => $s === SiteStatus::Online->value)->count();
        $uptime = round(($onlineCount / $total) * 100, 2);

        Log::info('Uptime calculated', [
            'site_id' => $this->site->id,
            'uptime' => $uptime,
            'total_checks' => $total,
            'online_checks' => $onlineCount,
        ]);

        return $uptime;
    }
}
