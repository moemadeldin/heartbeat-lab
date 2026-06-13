<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

final readonly class CreateSessionAction
{
    /**
     * @param  array<string>  $data
     */
    public function execute(array $data): User
    {

        throw_unless(
            Auth::attempt(['email' => $data['email'], 'password' => $data['password']]),
            AuthenticationException::class,
            'Invalid Credentials',
        );
        Log::info('User Logged in.', [
            'user_id' => Auth::id(),
            'ip_address' => request()->ip(),
            'context' => 'auth_flow',
        ]);

        return User::query()->findOrFail(Auth::id());
    }
}
