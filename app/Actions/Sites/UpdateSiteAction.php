<?php

declare(strict_types=1);

namespace App\Actions\Sites;

use App\Exceptions\DuplicateSiteException;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Log;

final readonly class UpdateSiteAction
{
    /**
     * @param  array{name: string, url: string}  $data
     */
    public function execute(User $user, Site $site, array $data): Site
    {
        throw_if(Site::whereNameDuplicate($user, $data['name'])
            ->exists(), DuplicateSiteException::class, 'name'
        );

        $site->update($data);

        Log::info('Site Updated By: ', [
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'context' => 'site_update_flow',
        ]);

        return $site;
    }
}
