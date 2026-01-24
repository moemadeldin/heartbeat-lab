<?php

declare(strict_types=1);

use App\Livewire\Dashboard;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders the dashboard component', function (): void {
    Livewire::test(Dashboard::class)
        ->assertStatus(200)
        ->assertSee('Heartbeat Lab')
        ->assertSee($this->user->name);
});

it('shows empty state when no sites are monitored', function (): void {
    Livewire::test(Dashboard::class)
        ->assertSee('No websites monitored yet')
        ->assertSee('Add Your First Site');
});

it('displays stats cards', function (): void {
    Livewire::test(Dashboard::class)
        ->assertSee('Total Sites')
        ->assertSee('Online')
        ->assertSee('Offline')
        ->assertSee('Uptime');
});

it('requires authentication', function (): void {
    auth()->logout();

    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});
