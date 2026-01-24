<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\Auth\DeleteSessionAction;
use Livewire\Component;

final class Logout extends Component
{
    public function logout(DeleteSessionAction $action): void
    {
        $action->execute();
        $this->redirect(route('login'));
    }

    public function render(): string
    {
        return <<<'HTML'
        <button 
            wire:click="logout"
            class="bg-red-600 text-white px-5 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors cursor-pointer shadow-md hover:shadow-lg">
            Logout
        </button>
        HTML;
    }
}
