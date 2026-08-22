<?php

declare(strict_types=1);

namespace App\Interfaces;

interface UrlValidator
{
    public function validateForPublicCheck(string $url): void;

    public function validateForMonitoring(string $url): void;
}
