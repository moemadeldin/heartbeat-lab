<?php

declare(strict_types=1);

namespace App\Livewire;

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

        $this->redirectRoute('public.status.show', ['url' => $this->url]);
    }

    public function render(): Factory|View
    {
        return view('livewire.public-status');
    }
}
