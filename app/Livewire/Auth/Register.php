<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\Auth\CreateUserAction;
use App\Events\UserRegistered;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Title('Register')]
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
        $this->validate();

        $user = $action->execute([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ]);
        Auth::login($user);
        event(new UserRegistered($user));

        $this->redirectRoute('dashboard', navigate: true);
    }

    // public function render(): Factory|View
    // {
    //     return view('livewire.auth.register');
    // }
}
