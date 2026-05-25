<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CheckSslJob;
use App\Models\Site;
use Illuminate\Console\Command;

final class CheckSslCertificatesCommand extends Command
{
    protected $signature = 'sites:ssl-check';

    protected $description = 'Check SSL certificates for all monitored websites';

    public function handle(): int
    {
        $this->info('Starting SSL certificate checks...');

        $sites = Site::query()
            ->where('url', 'like', 'https://%')
            ->get();

        if ($sites->isEmpty()) {
            $this->info('No HTTPS sites found to check.');

            return Command::SUCCESS;
        }

        foreach ($sites as $site) {
            $this->info(sprintf('Dispatching SSL check for: %s (%s)', $site->name, $site->url));
            dispatch(new CheckSslJob($site));
        }

        $this->info(sprintf('Dispatched %d SSL checks to queue.', $sites->count()));

        return Command::SUCCESS;
    }
}
