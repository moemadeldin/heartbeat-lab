<?php

declare(strict_types=1);

use App\Console\Commands\CheckSslCertificatesCommand;
use App\Console\Commands\ScheduleSiteChecksCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(ScheduleSiteChecksCommand::class)->everyFiveMinutes();
Schedule::command(CheckSslCertificatesCommand::class)->dailyAt('03:00');
