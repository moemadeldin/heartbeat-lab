<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class DuplicateSiteException extends Exception
{
    public function __construct(public readonly string $field = 'url') {}
}
