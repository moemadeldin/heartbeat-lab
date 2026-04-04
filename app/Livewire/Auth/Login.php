<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\Auth\CreateSessionAction;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]
final class Login extends Component
{
    #[Validate(['required', 'string', 'email:rfc,dns'])]
    public string $email = '';

    #[Validate(['required', 'string', 'min:8', 'max:88'])]
    public string $password = '';

    public function login(CreateSessionAction $action): void
    {
        /** @var array<string> $validated */
        $validated = $this->validate();

        try {
            $action->execute($validated);
            $this->redirectRoute('dashboard');

        } catch (AuthenticationException $authenticationException) {
            $this->addError('email', $authenticationException->getMessage());
            $this->addError('password', $authenticationException->getMessage());
        }

    }

    public function render(): Factory|View
    {
        return view('livewire.auth.login');
    }
}
