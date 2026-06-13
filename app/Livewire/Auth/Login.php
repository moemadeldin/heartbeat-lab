<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\Auth\CreateSessionAction;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Login')]
#[Layout('layouts.auth')]
final class Login extends Component
{
    #[Validate(['required', 'string', 'email:rfc,dns'])]
    public string $email = '';

    #[Validate(['required', 'string', 'min:8', 'max:88'])]
    public string $password = '';

    public function login(CreateSessionAction $action): void
    {

        $this->validate();

        try {
            $user = $action->execute([
                'email' => $this->email,
                'password' => $this->password,
            ]);
        } catch (AuthenticationException $authenticationException) {
            $this->addError('email', $authenticationException->getMessage());

            return;
        }

        Auth::login($user);
        $this->redirectIntended(default: route('dashboard'), navigate: true);
    }
}
