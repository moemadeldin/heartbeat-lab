<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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

        $userId = $this->authUser()->id;

        $stats = DB::table('sites')
            ->where('user_id', $userId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_online = true THEN 1 ELSE 0 END) as online')
            ->selectRaw('SUM(CASE WHEN is_online = false THEN 1 ELSE 0 END) as offline')
            ->selectRaw('COALESCE(AVG(uptime), 0) as uptime')
            ->first();

        $result = [
            'total' => (int) $stats->total,
            'online' => (int) $stats->online,
            'offline' => (int) $stats->offline,
            'uptime' => round((float) $stats->uptime, 2),
        ];

        Cache::store('redis')->set($this->statsCacheKey(), $result, 60);

        return $result;
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
