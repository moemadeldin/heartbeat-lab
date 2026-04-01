<?php

declare(strict_types=1);

use App\Livewire\Dashboard;
use App\Models\Site;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('renders the dashboard', function (): void {
    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->assertStatus(200);
});

it('refreshes sites on site-created event', function (): void {
    Site::factory()->for($this->user)->count(3)->create();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->dispatch('site-created')
        ->assertStatus(200);
});

it('refreshes sites on site-updated event', function (): void {
    Site::factory()->for($this->user)->count(2)->create();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->dispatch('site-updated')
        ->assertStatus(200);
});

it('refreshes sites on site-deleted event', function (): void {
    Site::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->dispatch('site-deleted')
        ->assertStatus(200);
});

it('edits a site', function (): void {
    $site = Site::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->call('editSite', $site->id)
        ->assertSet('selectedSiteId', $site->id);
});

it('confirms delete', function (): void {
    $site = Site::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->call('confirmDelete', $site->id)
        ->assertSet('deleteId', $site->id);
});

it('closes modals on close-modal event', function (): void {
    $site = Site::factory()->for($this->user)->create();

    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->call('confirmDelete', $site->id)
        ->assertSet('deleteId', $site->id)
        ->dispatch('close-modal')
        ->assertSet('selectedSiteId', null)
        ->assertSet('deleteId', null);
});

it('shows empty state', function (): void {
    Livewire::actingAs($this->user)
        ->test(Dashboard::class)
        ->assertSee('No');
});
