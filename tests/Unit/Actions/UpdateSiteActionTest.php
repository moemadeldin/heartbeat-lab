<?php

declare(strict_types=1);

use App\Actions\Sites\UpdateSiteAction;
use App\Exceptions\DuplicateSiteException;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->site = Site::factory()->for($this->user)->create();
});

it('updates a site', function (): void {
    $action = new UpdateSiteAction();

    Auth::login($this->user);

    $result = $action->execute($this->user, $this->site, [
        'name' => 'Updated Site',
        'url' => 'https://updated.example.com',
    ]);

    $this->assertEquals('Updated Site', $result->name);
    $this->assertEquals('https://updated.example.com', $result->url);
});

it('throws exception for duplicate site name', function (): void {
    $action = new UpdateSiteAction();

    Site::factory()->for($this->user)->create([
        'name' => 'Existing Site',
        'url' => 'https://existing.example.com',
    ]);

    Auth::login($this->user);

    $action->execute($this->user, $this->site, [
        'name' => 'Existing Site',
        'url' => 'https://new.example.com',
    ]);
})->throws(DuplicateSiteException::class);

it('logs site update', function (): void {
    $action = new UpdateSiteAction();

    Auth::login($this->user);

    Log::shouldReceive('info')->once();

    $action->execute($this->user, $this->site, [
        'name' => 'Updated Site',
        'url' => 'https://updated.example.com',
    ]);
});
