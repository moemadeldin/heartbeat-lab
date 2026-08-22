<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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
    }

    #[On(['site-created', 'site-updated', 'site-deleted'])]
    public function refreshSitesAndCloseModals(): void
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
        return Site::query()
            ->userSites($this->authUser())
            ->orderBy('created_at', 'asc')
            ->select(['id', 'user_id', 'name', 'url', 'status', 'ssl_valid', 'ssl_expires_at', 'ssl_issuer'])
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
        $userId = $this->authUser()->id;

        $stats = DB::table('sites')
            ->where('user_id', $userId)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'online' THEN 1 ELSE 0 END) as online")
            ->selectRaw("SUM(CASE WHEN status = 'offline' THEN 1 ELSE 0 END) as offline")
            ->first();

        $uptime = DB::table('site_checks')
            ->join('sites', 'sites.id', '=', 'site_checks.site_id')
            ->where('sites.user_id', $userId)
            ->selectRaw("COALESCE(AVG(CASE WHEN site_checks.status = 'online' THEN 100.0 ELSE 0 END), 0) as uptime")
            ->value('uptime');

        return [
            'total' => (int) $stats->total,
            'online' => (int) $stats->online,
            'offline' => (int) $stats->offline,
            'uptime' => round((float) $uptime, 2),
        ];
    }

    private function authUser(): User
    {
        return Auth::user();
    }
}
