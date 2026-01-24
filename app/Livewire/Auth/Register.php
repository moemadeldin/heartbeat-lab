<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\Auth\CreateUserAction;
use App\Events\UserRegistered;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.auth')]

final class Register extends Component
{
    #[Validate(['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'])]
    public string $name = '';

    #[Validate(['required', 'string', 'email:rfc,dns', 'unique:users,email'])]
    public string $email = '';

    #[Validate(['required', 'string', 'confirmed', 'min:8', 'max:88'])]
    public string $password = '';

    public string $password_confirmation = '';

    public function register(CreateUserAction $action): void
    {
        /** @var array<string> $validated */
        $validated = $this->validate();

        $user = $action->execute($validated);
        Auth::login($user);
        session()->regenerate();
        event(new UserRegistered($user));

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function render(): Factory|View
    {
        return view('livewire.auth.register');
    }
}
