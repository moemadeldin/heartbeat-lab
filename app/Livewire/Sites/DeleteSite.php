<?php

declare(strict_types=1);

namespace App\Livewire\Sites;

use App\Actions\Sites\DeleteSiteAction;
use App\Models\Site;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

final class DeleteSite extends Component
{
    public Site $site;

    public function mount(Site $site): void
    {
        abort_if($site->user_id !== Auth::id(), Response::HTTP_FORBIDDEN);
    }

    public function delete(DeleteSiteAction $action): void
    {
        $action->execute($this->site);

        // Dispatch events in correct order
        $this->dispatch('site-deleted');
        $this->dispatch('close-modal');
    }

    public function placeholder(): string
    {
        return <<<'HTML'
        <div class="animate-pulse space-y-4">
            <div class="h-6 bg-gray-700 rounded w-1/3"></div>
            <div class="h-10 bg-gray-700 rounded"></div>
            <div class="h-10 bg-gray-700 rounded"></div>
        </div>
        HTML;
    }
}
