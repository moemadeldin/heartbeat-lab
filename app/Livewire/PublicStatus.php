<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Site;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class PublicStatus extends Component
{
    public string $url = '';

    public function search(): void
    {
        $this->validate([
            'url' => ['required', 'url'],
        ]);

        $site = Site::query()->where('url', $this->url)->first();

        if ($site === null) {
            $this->addError('url', 'No monitored site found with that URL.');

            return;
        }

        $this->redirectRoute('public.status.show', $site);
    }

    public function render(): Factory|View
    {
        return view('livewire.public-status');
    }
}
