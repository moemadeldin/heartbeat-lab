<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;

final readonly class DeleteSessionAction
{
    public function execute(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }
}
