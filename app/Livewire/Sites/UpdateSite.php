<?php

declare(strict_types=1);

namespace App\Livewire\Sites;

use App\Actions\Sites\UpdateSiteAction;
use App\Exceptions\DuplicateSiteException;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class UpdateSite extends Component
{
    #[Validate(['required', 'string', 'max:255'])]
    public string $name = '';

    public Site $site;

    public function mount(Site $site): void
    {
        abort_if($site->user_id !== Auth::id(), Response::HTTP_FORBIDDEN);
        $this->name = $this->site->name;
    }

    public function update(UpdateSiteAction $action): void
    {
        /** @var array{name: string} $validated */
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
