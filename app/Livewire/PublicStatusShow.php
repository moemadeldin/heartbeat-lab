<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Site;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class PublicStatusShow extends Component
{
    public Site $site;

    public function render(): Factory|View
    {
        return view('livewire.public-status-show');
    }
}
