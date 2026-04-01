<?php

declare(strict_types=1);

namespace App\Livewire\Sites;

use App\Actions\Sites\UpdateSiteAction;
use App\Exceptions\DuplicateSiteException;
use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class UpdateSite extends Component
{
    #[Validate(['required', 'string', 'max:255'])]
    public string $name = '';

    public Site $site;

    public function mount(string $siteId): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->site = Site::query()
            ->userSites($user)
            ->findOrFail($siteId);

        $this->name = $this->site->name;
    }

    public function update(UpdateSiteAction $action): void
    {
        /** @var array{name: string, url: string} $validated */
        $validated = $this->validate();
        /** @var User $user */
        $user = Auth::user();
        try {
            $action->execute($user, $this->site, $validated);
        } catch (DuplicateSiteException $duplicateSiteException) {
            $message = $duplicateSiteException->field === 'name'
                ? 'You are already monitoring a site with this name.'
                : 'Invalid update.';

            $this->addError($duplicateSiteException->field, $message);

            return;
        }

        $this->dispatch('site-updated');
        $this->dispatch('close-modal');

        $this->reset(['name']);
    }

    public function render(): View
    {
        return view('livewire.sites.update-site');
    }
}
