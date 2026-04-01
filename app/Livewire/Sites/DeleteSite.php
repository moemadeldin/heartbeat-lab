<?php

declare(strict_types=1);

namespace App\Livewire\Sites;

use App\Actions\Sites\DeleteSiteAction;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

final class DeleteSite extends Component
{
    public Site $site;

    public function mount(?string $siteId): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->site = Site::query()
            ->userSites($user)
            ->findOrFail($siteId);
    }

    public function delete(DeleteSiteAction $action): void
    {
        $action->execute($this->site);

        // Dispatch events in correct order
        $this->dispatch('site-deleted');
        $this->dispatch('close-modal');
    }

    public function render(): View
    {
        return view('livewire.sites.delete-site');
    }
}
