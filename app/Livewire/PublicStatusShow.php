<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Site;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Redis;
use Livewire\Component;

final class PublicStatusShow extends Component
{
    public Site $site;

    /** @var array<string, mixed>|null */
    public ?array $cachedStatus = null;

    public function mount(): void
    {
        $data = Redis::get(sprintf('site:%s:status', $this->site->id));

        if (is_string($data)) {
            /** @var array<string, mixed>|null $decoded */
            $decoded = json_decode($data, true);

            if (is_array($decoded)) {
                $this->cachedStatus = $decoded;
            }
        }
    }

    public function render(): Factory|View
    {
        return view('livewire.public-status-show');
    }
}
