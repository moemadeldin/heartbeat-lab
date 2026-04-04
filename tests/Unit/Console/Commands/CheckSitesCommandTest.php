<?php

declare(strict_types=1);

use App\Jobs\CheckSiteJob;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('checks all sites and dispatches jobs', function (): void {
    Queue::fake();

    Site::factory()->for($this->user)->create(['url' => 'https://example1.com']);
    Site::factory()->for($this->user)->create(['url' => 'https://example2.com']);

    $this->artisan('sites:check');

    Queue::assertPushed(CheckSiteJob::class, 2);
});

it('handles no sites scenario', function (): void {
    $this->artisan('sites:check')->assertExitCode(0);
});
