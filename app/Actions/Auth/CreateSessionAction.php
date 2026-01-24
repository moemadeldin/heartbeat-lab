<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

final readonly class CreateSessionAction
{
    /**
     * @param  array<string>  $data
     */
    public function execute(array $data): User
    {
        $user = User::whereEmail($data['email'])->first();
        throw_unless($user, AuthenticationException::class, 'Invalid Credentials');

        throw_unless(Hash::check($data['password'], $user->password), AuthenticationException::class, 'Invalid Credentials');

        Auth::login($user);
        session()->regenerate();

        Log::info('User Logged in.', [
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'context' => 'auth_flow',
        ]);

        return $user;
    }
}
