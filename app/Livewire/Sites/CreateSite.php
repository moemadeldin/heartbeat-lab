<?php

declare(strict_types=1);

namespace App\Livewire\Sites;

use App\Actions\Sites\CreateSiteAction;
use App\Exceptions\DuplicateSiteException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class CreateSite extends Component
{
    #[Validate(['required', 'string', 'max:255'])]
    public string $name = '';

    #[Validate(['required', 'string', 'url'])]
    public string $url = '';

    public function store(CreateSiteAction $action): void
    {
        /** @var array{name: string, url: string} $validated */
        $validated = $this->validate();

        /** @var User $user */
        $user = Auth::user();

        try {
            $action->execute($user, $validated);
        } catch (DuplicateSiteException $duplicateSiteException) {
            $message = $duplicateSiteException->field === 'name'
                ? 'You are already monitoring a site with this name.'
                : 'You are already monitoring this URL.';

            $this->addError($duplicateSiteException->field, $message);

            return;
        }

        $this->reset(['name', 'url']);
        $this->dispatch('site-created');
        $this->dispatch('close-modal');
        $this->dispatch('close-create-modal');
    }

    public function render(): View
    {
        return view('livewire.sites.create-site');
    }
}
