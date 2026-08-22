<?php

declare(strict_types=1);

use App\Interfaces\UrlValidator;
use App\Jobs\CheckSiteJob;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->site = Site::factory()->for($this->user)->create([
        'url' => 'https://example.com',
    ]);

    $mock = Mockery::mock(UrlValidator::class);
    $mock->shouldReceive('validateForMonitoring')->andReturnNull();

    $this->app->instance(UrlValidator::class, $mock);
});

it('updates site status when online', function (): void {
    Http::fake([
        '*' => Http::response('', 200),
    ]);

    $job = new CheckSiteJob($this->site);
    $job->handle(resolve(UrlValidator::class));

    $this->site->refresh();

    $this->assertTrue($this->site->is_online);
    $this->assertEquals(200, $this->site->status_code);
});

it('updates site status when offline', function (): void {
    Http::fake([
        '*' => Http::response('', 500),
    ]);

    $job = new CheckSiteJob($this->site);
    $job->handle(resolve(UrlValidator::class));

    $this->site->refresh();

    $this->assertFalse($this->site->is_online);
    $this->assertEquals(500, $this->site->status_code);
});

it('handles connection error', function (): void {
    Log::spy();

    Http::fake(function ($request): void {
        throw new ConnectionException('Connection failed');
    });

    $job = new CheckSiteJob($this->site);
    $job->handle(resolve(UrlValidator::class));

    $this->site->refresh();

    $this->assertFalse($this->site->is_online);

    Log::shouldHaveReceived('error')
        ->once()
        ->with('Site check failed', Mockery::subset(['site_id' => $this->site->id]));
});

it('logs successful check', function (): void {
    Log::spy();

    Http::fake([
        '*' => Http::response('', 200),
    ]);

    $job = new CheckSiteJob($this->site);
    $job->handle(resolve(UrlValidator::class));

    Log::shouldHaveReceived('info')->with('Site checked', Mockery::any());
    Log::shouldHaveReceived('info')->with('Uptime calculated', Mockery::any());
});

it('marks site online when response successful with 2xx', function (): void {
    Http::fake([
        '*' => Http::response('', 201),
    ]);

    $job = new CheckSiteJob($this->site);
    $job->handle(resolve(UrlValidator::class));

    $this->site->refresh();

    $this->assertTrue($this->site->is_online);
    $this->assertEquals(201, $this->site->status_code);
});

it('marks site offline when response redirect', function (): void {
    Http::fake([
        '*' => Http::response('', 301),
    ]);

    $job = new CheckSiteJob($this->site);
    $job->handle(resolve(UrlValidator::class));

    $this->site->refresh();

    $this->assertFalse($this->site->is_online);
    $this->assertEquals(301, $this->site->status_code);
});
it('updates site status and calculates uptime', function (): void {
    Redis::shouldReceive('setex')->once();

    Http::fake(['*' => Http::response('', 200)]);
    Log::spy();

    $job = new CheckSiteJob($this->site);
    $job->handle(resolve(UrlValidator::class));

    $this->site->refresh();

    $this->assertEquals(100, $this->site->uptime);

    $checksInDb = DB::table('site_checks')->where('site_id', $this->site->id)->count();
    $this->assertEquals(1, $checksInDb);
});
