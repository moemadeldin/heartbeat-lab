<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

final class ScheduleSiteChecksCommand extends Command
{
    protected $signature = 'sites:schedule-checks';

    protected $description = 'Schedule periodic checks for all websites (delegates to sites:check)';

    public function handle(): int
    {
        return $this->call('sites:check');
    }
}
