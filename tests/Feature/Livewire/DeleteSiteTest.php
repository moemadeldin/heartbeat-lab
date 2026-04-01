<?php

declare(strict_types=1);

use App\Livewire\Sites\DeleteSite;
use App\Models\Site;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->site = Site::factory()->for($this->user)->create();
});

it('deletes a site', function (): void {
    Livewire::actingAs($this->user)
        ->test(DeleteSite::class, ['siteId' => $this->site->id])
        ->call('delete')
        ->assertDispatched('site-deleted')
        ->assertDispatched('close-modal');

    $this->assertDatabaseMissing('sites', [
        'id' => $this->site->id,
    ]);
});
