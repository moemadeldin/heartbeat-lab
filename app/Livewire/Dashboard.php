<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Site;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Site> $sites
 */
final class Dashboard extends Component
{
    #[Locked]
    public ?string $selectedSiteId = null;

    #[Locked]
    public ?string $deleteId = null;

    #[On('site-created')]
    #[On('site-updated')]
    #[On('site-deleted')]
    #[On('site-status-updated')]
    public function refreshSites(): void
    {
        unset($this->sites, $this->stats);
        $this->selectedSiteId = null;
        $this->deleteId = null;
    }

    public function editSite(string $siteId): void
    {
        $this->selectedSiteId = $siteId;
    }

    public function confirmDelete(string $siteId): void
    {
        $this->deleteId = $siteId;
    }

    #[On('close-modal')]
    public function closeModals(): void
    {
        $this->selectedSiteId = null;
        $this->deleteId = null;
    }

    /**
     * @return Collection<int, Site>
     */
    #[Computed]
    public function sites(): Collection
    {
        /** @var User $user */
        $user = Auth::user();

        return Site::query()
            ->userSites($user)
            ->orderBy('created_at')
            ->distinct()
            ->get();
    }

    /**
     * @return array{
     *     total: int,
     *     online: int,
     *     offline: int,
     *     uptime: float
     * }
     */
    #[Computed]
    public function stats(): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, Site> $sites */
        $sites = $this->sites;

        return [
            'total' => $sites->count(),
            'online' => $sites->where('is_online', true)->count(),
            'offline' => $sites->where('is_online', false)->count(),
            'uptime' => $sites->avg('uptime') ?? 0.00,
        ];
    }

    public function render(): Factory|View
    {
        return view('livewire.dashboard');
    }
}
