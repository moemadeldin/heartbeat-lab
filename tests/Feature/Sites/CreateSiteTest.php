<?php

declare(strict_types=1);

use App\Livewire\Sites\CreateSite;
use App\Models\Site;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('can create a new site', function (): void {
    Livewire::test(CreateSite::class)
        ->set('name', 'My Awesome App')
        ->set('url', 'https://google.com')
        ->call('store')
        ->assertHasNoErrors()
        ->assertSet('name', '')
        ->assertSet('url', '')
        ->assertDispatched('site-created')
        ->assertDispatched('close-modal');

    expect(Site::query()->count())->toBe(1);
    expect(Site::query()->first()->name)->toBe('My Awesome App');
});

it('validates required fields', function (): void {
    Livewire::test(CreateSite::class)
        ->call('store')
        ->assertHasErrors([
            'name' => 'required',
            'url' => 'required',
        ]);
});

it('validates that url is a valid url', function (): void {
    Livewire::test(CreateSite::class)
        ->set('name', 'Invalid Site')
        ->set('url', 'not-a-url')
        ->call('store')
        ->assertHasErrors(['url' => 'url']);
});

it('fails to create a site if name already exists', function (): void {
    Site::factory()->create(['user_id' => $this->user->id, 'name' => 'My Awesome App']);

    Livewire::test(CreateSite::class)
        ->set('name', 'My Awesome App')
        ->set('url', 'https://google.com')
        ->call('store')
        ->assertHasErrors(['name' => 'You are already monitoring a site with this name.']);
});
it('fails to create a site if url already exists', function (): void {
    Site::factory()->create(['user_id' => $this->user->id, 'name' => 'My Awesome App', 'url' => 'https://google.com']);

    Livewire::test(CreateSite::class)
        ->set('name', 'My Awesome App2')
        ->set('url', 'https://google.com')
        ->call('store')
        ->assertHasErrors(['url' => 'You are already monitoring this URL.']);
});
