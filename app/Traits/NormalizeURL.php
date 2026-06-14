<?php

declare(strict_types=1);

namespace App\Traits;

trait NormalizeURL
{
    protected function normalize(string $url): string
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
