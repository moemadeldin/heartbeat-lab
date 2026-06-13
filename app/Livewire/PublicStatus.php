<?php

declare(strict_types=1);

namespace App\Livewire;

use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Public Status Checker')]
final class PublicStatus extends Component
{
    #[Validate('required', 'url')]
    public string $url = '';

    public function search(): void
    {
        $this->validate();

        $this->redirectRoute('public.status.show', ['url' => $this->url]);
    }
}
