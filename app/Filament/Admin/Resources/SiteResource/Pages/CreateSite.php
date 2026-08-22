<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SiteResource\Pages;

use App\Filament\Admin\Resources\SiteResource;
use App\Jobs\CheckSiteJob;
use App\Jobs\CheckSslJob;
use App\Models\Site;
use Filament\Resources\Pages\CreateRecord;

final class CreateSite extends CreateRecord
{
    protected static string $resource = SiteResource::class;

    protected function afterCreate(): void
    {
        /** @var Site $site */
        $site = $this->record;

        dispatch(new CheckSiteJob($site));
        dispatch(new CheckSslJob($site));
    }
}
