<?php

declare(strict_types=1);

namespace App\Actions\Sites;

use App\Exceptions\DuplicateSiteException;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Log;

final readonly class CreateSiteAction
{
    /**
     * @param  array{name: string, url: string}  $data
     */
    public function execute(User $user, array $data): Site
    {
        throw_if(Site::whereNameDuplicate($user, $data['name'])
            ->exists(), DuplicateSiteException::class, 'name'
        );

        throw_if(
            Site::whereURLDuplicate($user, $data['url'])
                ->exists(), DuplicateSiteException::class, 'url'
        );

        $site = $user->sites()->create($data);

        Log::info('Site Created By: ', [
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'context' => 'site_creation_flow',
        ]);

        return $site;
    }
}
