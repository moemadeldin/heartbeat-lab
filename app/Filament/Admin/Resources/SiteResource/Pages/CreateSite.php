<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SiteResource\Pages;

use App\Filament\Admin\Resources\SiteResource;
use Filament\Resources\Pages\CreateRecord;

final class CreateSite extends CreateRecord
{
    protected static string $resource = SiteResource::class;
}
