<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Support\Carbon;

final readonly class SslCertificateService
{
    /**
     * @return array{ssl_valid: bool|null, ssl_issuer: string|null, ssl_days_left: int|null}
     */
    public function check(string $url): array
    {
        $parsed = $this->parseUrl($url);

        if ($parsed === null) {
            return ['ssl_valid' => null, 'ssl_issuer' => null, 'ssl_days_left' => null];
        }

        $certInfo = $this->fetchCertInfo($parsed['host']);

        if ($certInfo === null) {
            return ['ssl_valid' => false, 'ssl_issuer' => null, 'ssl_days_left' => null];
        }

        return $this->buildResult($certInfo);
    }

    /**
     * @return array{ssl_valid: bool|null, ssl_issuer: string|null, ssl_days_left: int|null, ssl_expires_at: Carbon|null}
     */
    public function checkWithExpiry(string $url): array
    {
        $parsed = $this->parseUrl($url);

        if ($parsed === null) {
            return [
                'ssl_valid' => null,
                'ssl_issuer' => null,
                'ssl_days_left' => null,
                'ssl_expires_at' => null,
            ];
        }

        $certInfo = $this->fetchCertInfo($parsed['host']);

        if ($certInfo === null) {
            return [
                'ssl_valid' => false,
                'ssl_issuer' => null,
                'ssl_days_left' => null,
                'ssl_expires_at' => null,
            ];
        }

        $result = $this->buildResult($certInfo);

        $result['ssl_expires_at'] = $certInfo['validTo_time_t'] > 0
            ? now()->setTimestamp($certInfo['validTo_time_t'])
            : null;

        return $result;
    }

    /**
     * @return array{host: string}|null
     */
    private function parseUrl(string $url): ?array
    {
        $parsed = parse_url($url);

        if (($parsed['scheme'] ?? '') !== 'https') {
            return null;
        }

        $host = $parsed['host'] ?? '';

        if ($host === '') {
            return null;
        }

        return ['host' => $host];
    }

    /**
     * @return array{validTo_time_t: int, issuer: array{O?: string, CN?: string}}|null
     */
    private function fetchCertInfo(string $host): ?array
    {
        try {
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ]);

            $socket = stream_socket_client(
                sprintf('ssl://%s:443', $host),
                $errno,
                $errstr,
                10,
                STREAM_CLIENT_CONNECT,
                $context,
            );

            if ($socket === false) {
                return null;
            }

            $params = stream_context_get_params($socket);
            fclose($socket);

            $cert = $params['options']['ssl']['peer_certificate'] ?? null;

            if ($cert === null) {
                return null;
            }

            if (! $cert instanceof OpenSSLCertificate) {
                $cert = openssl_x509_read($cert);
            }

            $certInfo = openssl_x509_parse($cert);

            if ($certInfo === false) {
                return null;
            }

            /** @var array{validTo_time_t?: int, issuer?: array{O?: string, CN?: string}} $certInfo */
            return [
                'validTo_time_t' => (int) ($certInfo['validTo_time_t'] ?? 0),
                'issuer' => $certInfo['issuer'] ?? ['CN' => 'Unknown'],
            ];
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @param  array{validTo_time_t: int, issuer: array{O?: string, CN?: string}}  $certInfo
     * @return array{ssl_valid: bool|null, ssl_issuer: string|null, ssl_days_left: int|null}
     */
    private function buildResult(array $certInfo): array
    {
        $expiresAt = $certInfo['validTo_time_t'];
        $daysLeft = $expiresAt > 0 ? (int) ceil(($expiresAt - time()) / 86400) : null;

        return [
            'ssl_valid' => $daysLeft !== null && $daysLeft > 0,
            'ssl_issuer' => (string) ($certInfo['issuer']['O'] ?? $certInfo['issuer']['CN'] ?? 'Unknown'),
            'ssl_days_left' => $daysLeft,
        ];
    }
}
