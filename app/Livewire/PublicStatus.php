<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Traits\NormalizeURL;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Public Status Checker')]
final class PublicStatus extends Component
{
    use NormalizeURL;

    #[Validate(['required', 'string', 'url_or_domain'])]
    public string $url = '';

    public function search(): void
    {
        $this->validate();

        $this->url = $this->normalize($this->url);

        $this->redirectRoute('public.status.show', ['url' => $this->url]);
    }
}
