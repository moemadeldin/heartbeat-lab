<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ValidUrlOrDomain implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid URL or domain (e.g., example.com or https://example.com).');

            return;
        }

        $normalized = $this->normalize($value);

        if (filter_var($normalized, FILTER_VALIDATE_URL) === false) {
            $fail('The :attribute must be a valid URL or domain (e.g., example.com or https://example.com).');
        }
    }

    private function normalize(string $url): string
    {
        $url = mb_trim($url);

        if ($url === '') {
            return '';
        }

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            return 'https://'.$url;
        }

        return $url;
    }
}
