<?php

declare(strict_types=1);

use App\Livewire\Sites\UpdateSite;
use App\Models\Site;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->site = Site::factory()->for($this->user)->create();
});

it('renders the update site form', function (): void {
    Livewire::actingAs($this->user)
        ->test(UpdateSite::class, ['siteId' => $this->site->id])
        ->assertStatus(200);
});

it('updates a site', function (): void {
    Livewire::actingAs($this->user)
        ->test(UpdateSite::class, ['siteId' => $this->site->id])
        ->set('name', 'Updated Site Name')
        ->call('update')
        ->assertDispatched('site-updated')
        ->assertDispatched('close-modal');

    $this->site->refresh();
    $this->assertEquals('Updated Site Name', $this->site->name);
});

it('validates name is required', function (): void {
    Livewire::actingAs($this->user)
        ->test(UpdateSite::class, ['siteId' => $this->site->id])
        ->set('name', '')
        ->call('update')
        ->assertHasErrors(['name' => 'required']);
});

it('fails to update site if name already exists', function (): void {
    Site::factory()->create(['user_id' => $this->user->id, 'name' => 'Existing Site']);

    Livewire::actingAs($this->user)
        ->test(UpdateSite::class, ['siteId' => $this->site->id])
        ->set('name', 'Existing Site')
        ->call('update')
        ->assertHasErrors(['name' => 'You are already monitoring a site with this name.']);
});
