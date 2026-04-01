<?php

declare(strict_types=1);

namespace App\Actions\Sites;

use App\Models\Site;
use Illuminate\Support\Facades\Log;

final readonly class DeleteSiteAction
{
    public function execute(Site $site): void
    {
        $site->delete();

        Log::info('Site Deleted By: ', [
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'context' => 'site_deletion_flow',
        ]);
    }
}
