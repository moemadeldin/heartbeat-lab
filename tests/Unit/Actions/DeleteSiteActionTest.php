<?php

declare(strict_types=1);

use App\Actions\Sites\DeleteSiteAction;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->site = Site::factory()->for($this->user)->create();
});

it('deletes a site', function (): void {
    $action = new DeleteSiteAction();

    Auth::login($this->user);

    $action->execute($this->site);

    $this->assertDatabaseMissing('sites', [
        'id' => $this->site->id,
    ]);
});

it('logs site deletion', function (): void {
    $action = new DeleteSiteAction();

    Auth::login($this->user);

    Log::shouldReceive('info')->once();

    $action->execute($this->site);
});
