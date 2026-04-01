<?php

declare(strict_types=1);

use App\Livewire\Dashboard;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});
it('requires authentication', function (): void {
    auth()->logout();

    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
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

// it('displays user sites with correct stats', function (): void {
//     Site::factory()->for($this->user)->create([
//         'is_online' => true,
//         'uptime' => 99.5,
//     ]);
//     Site::factory()->for($this->user)->create([
//         'is_online' => true,
//         'uptime' => 98.0,
//     ]);
//     Site::factory()->for($this->user)->create([
//         'is_online' => false,
//         'uptime' => 85.0,
//     ]);

//     $sites = Site::userSites($this->user)->get();

//     Livewire::test(Dashboard::class)
//         ->assertViewHas('sites', fn ($viewSites): bool => $viewSites->count() === 3)
//         ->assertViewHas('stats', fn (array $stats): bool => $stats['total'] === $sites->count()
//             && $stats['online'] === $sites->where('is_online', true)->count()
//             && $stats['offline'] === $sites->where('is_online', false)->count()
//             && (float) $stats['uptime'] === (float) $sites->avg('uptime'));
// });
// it('refreshes the sites list when site-created event is heard', function (): void {
//     $component = Livewire::test(Dashboard::class);

//     expect($component->viewData('sites'))->toHaveCount(0);

//     Site::factory()->for($this->user)->create();

//     $component->dispatch('site-created');

//     expect($component->viewData('sites'))->toHaveCount(1);
// });
