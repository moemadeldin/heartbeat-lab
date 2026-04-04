<?php

declare(strict_types=1);

use App\Jobs\CheckSiteJob;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('schedules site checks', function (): void {
    Queue::fake();

    Site::factory()->for($this->user)->create(['url' => 'https://example.com']);
    Site::factory()->for($this->user)->create(['url' => 'https://example2.com']);

    $this->artisan('sites:schedule-checks');

    Queue::assertPushed(CheckSiteJob::class, 2);
});

it('handles no sites scenario', function (): void {
    $this->artisan('sites:schedule-checks')->assertExitCode(0);
});
