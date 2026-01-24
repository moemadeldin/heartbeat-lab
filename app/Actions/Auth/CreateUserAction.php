<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

final readonly class CreateUserAction
{
    /**
     * @param  array<string>  $data
     */
    public function execute(array $data): User
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make((string) $data['password']),
            'email_verified_at' => now(),
        ]);

        Log::info('New user registered', [
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'context' => 'auth_flow',
        ]);

        return $user;
    }
}
