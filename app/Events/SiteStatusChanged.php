<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Site;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class SiteStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Site $site,
        public bool $isOnline,
        public ?int $statusCode,
        public ?float $responseTime
    ) {}

    public function broadcastOn(): array
    {
        return [new Channel('private-user.'.$this->site->user_id)];
    }

    public function broadcastAs(): string
    {
        return 'SiteStatusChanged';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'site_id' => $this->site->id,
            'site_name' => $this->site->name,
            'is_online' => $this->isOnline,
            'status_code' => $this->statusCode,
            'response_time' => $this->responseTime,
            'last_checked_at' => $this->site->last_checked_at?->toIso8601String(),
        ];
    }
}
