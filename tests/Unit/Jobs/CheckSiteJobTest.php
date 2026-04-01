<?php

declare(strict_types=1);

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\ConnectionException;
use App\Jobs\CheckSiteJob;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->site = Site::factory()->for($this->user)->create([
        'url' => 'https://example.com',
    ]);
});

it('updates site status when online', function (): void {
    Http::fake([
        '*' => Http::response('', 200),
    ]);

    $job = new CheckSiteJob($this->site);
    $job->handle(new Factory);

    $this->site->refresh();

    $this->assertTrue($this->site->is_online);
    $this->assertEquals(200, $this->site->status_code);
});

it('updates site status when offline', function (): void {
    Http::fake([
        '*' => Http::response('', 500),
    ]);

    $job = new CheckSiteJob($this->site);
    $job->handle(new Factory);

    $this->site->refresh();

    $this->assertFalse($this->site->is_online);
    $this->assertEquals(500, $this->site->status_code);
});

it('handles connection error', function (): void {
    Http::fake(function ($request): void {
        throw new ConnectionException('Connection failed');
    });

    Log::shouldReceive('error')->once();

    $job = new CheckSiteJob($this->site);
    $job->handle(new Factory);

    $this->site->refresh();

    $this->assertFalse($this->site->is_online);
});

it('logs successful check', function (): void {
    Http::fake([
        '*' => Http::response('', 200),
    ]);

    Log::shouldReceive('info')->once();

    $job = new CheckSiteJob($this->site);
    $job->handle(new Factory);
});
