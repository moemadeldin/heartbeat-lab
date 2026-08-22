<?php

declare(strict_types=1);

namespace App\Actions\Sites;

use App\Models\Site;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

final readonly class DeleteSiteAction
{
    public function execute(Site $site): void
    {
        $siteId = $site->id;

        $site->delete();

        Redis::del([
            sprintf('site:%s:status', $siteId),
            sprintf('site:%s:checks', $siteId),
            sprintf('site:%s:ssl', $siteId),
            sprintf('site:%s:ssl:notified', $siteId),
        ]);

        Log::info('Site Deleted By: ', [
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'context' => 'site_deletion_flow',
        ]);
    }
}
