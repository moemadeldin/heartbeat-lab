<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Site> $sites
 */
#[Title('Dashboard')]
final class Dashboard extends Component
{
    #[Locked]
    public ?string $selectedSiteId = null;

    #[Locked]
    public ?string $deleteId = null;

    #[On(['site-created', 'site-updated', 'site-deleted', 'site-status-updated'])]
    public function refreshSites(): void
    {
        unset($this->sites, $this->stats);
        $this->selectedSiteId = null;
        $this->deleteId = null;

        Cache::store('redis')->forget($this->statsCacheKey());
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
        return Site::query()
            ->userSites($this->authUser())
            ->orderBy('created_at', 'asc')
            ->select(['id', 'user_id', 'name', 'url', 'is_online', 'uptime', 'ssl_valid', 'ssl_expires_at', 'ssl_issuer'])
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
        $cached = Cache::store('redis')->get($this->statsCacheKey());

        if ($cached !== null) {
            /** @var array{total: int, online: int, offline: int, uptime: float} $cached */
            return $cached;
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, Site> $sites */
        $sites = $this->sites;

        $stats = [
            'total' => $sites->count(),
            'online' => $sites->where('is_online', true)->count(),
            'offline' => $sites->where('is_online', false)->count(),
            'uptime' => $sites->avg('uptime') ?? 0.00,
        ];

        Cache::store('redis')->set($this->statsCacheKey(), $stats, 60);

        return $stats;
    }

    private function authUser(): User
    {
        return Auth::user();
    }

    private function statsCacheKey(): string
    {
        return sprintf('user:%s:dashboard:stats', Auth::id());
    }
}
