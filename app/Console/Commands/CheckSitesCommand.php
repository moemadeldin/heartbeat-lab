<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CheckSiteJob;
use App\Models\Site;
use Illuminate\Console\Command;

final class CheckSitesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sites:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of all monitored websites';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting site status checks...');

        $sites = Site::with('user')->get();

        if ($sites->isEmpty()) {
            $this->info('No sites found to check.');

            return Command::SUCCESS;
        }

        foreach ($sites as $site) {
            $this->info(sprintf('Dispatching check for: %s (%s)', $site->name, $site->url));
            dispatch(new CheckSiteJob($site));
        }

        $this->info(sprintf('Dispatched %d site checks to queue.', $sites->count()));

        return Command::SUCCESS;
    }
}
