<?php

declare(strict_types=1);

namespace App\Utilities;

use App\Interfaces\UrlValidator as UrlValidatorContract;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

final class UrlValidator implements UrlValidatorContract
{
    public function validateForPublicCheck(string $url): void
    {
        $this->ensureNotPrivate($url, 'public status check');
    }

    public function validateForMonitoring(string $url): void
    {
        $this->ensureNotPrivate($url, 'site monitoring');
    }

    private function ensureNotPrivate(string $url, string $context): void
    {
        $parsed = parse_url($url);

        if ($parsed === false || ! isset($parsed['host'])) {
            return;
        }

        $host = $parsed['host'];
        $ips = $this->resolveHost($host);

        foreach ($ips as $ip) {
            if ($this->isPrivateOrReserved($ip)) {
                Log::warning('Blocked private IP in '.$context, [
                    'url' => $url,
                    'host' => $host,
                    'resolved_ip' => $ip,
                ]);

                throw new InvalidArgumentException('Access to private/internal addresses is not allowed.');
            }
        }
    }

    /**
     * @return list<string>
     */
    private function resolveHost(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            return [$host];
        }

        $ips = @dns_get_record($host, DNS_A | DNS_AAAA);

        if ($ips === false || $ips === []) {
            return [];
        }

        return array_column($ips, 'ip');
    }

    private function isPrivateOrReserved(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    }
}
