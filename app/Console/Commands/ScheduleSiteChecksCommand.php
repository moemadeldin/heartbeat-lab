<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CheckSiteJob;
use App\Models\Site;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

final class ScheduleSiteChecksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sites:schedule-checks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule periodic checks for all websites';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scheduling periodic site checks...');

        Site::query()->with('user')->chunk(100, function (Collection $sites): void {
            foreach ($sites as $site) {
                dispatch(new CheckSiteJob($site));
                $this->line('Scheduled check for: '.$site->name);
            }

            $this->info(sprintf('Scheduled %d site checks to queue.', $sites->count()));
        });

        return Command::SUCCESS;
    }
}
