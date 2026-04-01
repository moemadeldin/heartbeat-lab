<?php

declare(strict_types=1);

use App\Livewire\Sites\CreateSite;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('renders the create site form', function (): void {
    Livewire::actingAs($this->user)
        ->test(CreateSite::class)
        ->assertStatus(200);
});

it('validates name is required', function (): void {
    Livewire::actingAs($this->user)
        ->test(CreateSite::class)
        ->set('url', 'https://example.com')
        ->call('store')
        ->assertHasErrors(['name' => 'required']);
});

it('validates url is required', function (): void {
    Livewire::actingAs($this->user)
        ->test(CreateSite::class)
        ->set('name', 'My Site')
        ->call('store')
        ->assertHasErrors(['url' => 'required']);
});

it('validates url must be valid', function (): void {
    Livewire::actingAs($this->user)
        ->test(CreateSite::class)
        ->set('name', 'My Site')
        ->set('url', 'not-a-url')
        ->call('store')
        ->assertHasErrors(['url' => 'url']);
});

it('creates a site successfully', function (): void {
    Livewire::actingAs($this->user)
        ->test(CreateSite::class)
        ->set('name', 'My Site')
        ->set('url', 'https://example.com')
        ->call('store')
        ->assertDispatched('site-created')
        ->assertDispatched('close-modal');

    $this->assertDatabaseHas('sites', [
        'name' => 'My Site',
        'url' => 'https://example.com',
    ]);
});
