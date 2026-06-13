<?php

declare(strict_types=1);

namespace App\Livewire;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use OpenSSLCertificate;

#[Title('Status Checker Result')]
#[Lazy()]
final class PublicStatusShow extends Component
{
    private const int ATTEMPTS_LIMIT_PER_IP = 10;

    private const int DECAY_SECONDS = 60;

    private const int HTTP_TIMEOUT = 10;

    private const string USER_AGENT = 'Heartbeat-Lab/1.0';

    private const string ACCEPT = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';

    #[Url(as: 'url')]
    public string $url = '';

    public ?bool $isOnline = null;

    public ?int $statusCode = null;

    public ?float $responseTime = null;

    public ?string $error = null;

    public ?bool $sslValid = null;

    public ?string $sslIssuer = null;

    public ?int $sslDaysLeft = null;

    public bool $checked = false;

    public function mount(): void
    {
        if ($this->url === '') {
            $this->redirectRoute('public.status');

            return;
        }

        if (! filter_var($this->url, FILTER_VALIDATE_URL)) {
            $this->error = 'Invalid URL provided.';

            return;
        }

        $key = 'status-check:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, self::ATTEMPTS_LIMIT_PER_IP)) {
            $this->error = 'Too many requests. Please wait.';

            return;
        }

        RateLimiter::hit($key, self::DECAY_SECONDS);

        $this->dispatch('do-check');
    }

    #[On('do-check')]
    public function performCheck(): void
    {
        if ($this->checked || $this->error !== null) {
            return;
        }

        $startTime = microtime(true);

        try {
            /** @var Response $response */
            $response = Http::timeout(self::HTTP_TIMEOUT)
                ->withHeaders([
                    'User-Agent' => self::USER_AGENT,
                    'Accept' => self::ACCEPT,
                ])
                ->get($this->url);

            $this->responseTime = round((microtime(true) - $startTime) * 1000, 2);
            $this->statusCode = $response->status();
            $this->isOnline = $response->successful();
        } catch (ConnectionException) {
            $this->isOnline = false;
            $this->statusCode = null;
            $this->responseTime = null;
            $this->error = 'Could not connect to host.';
        } catch (Exception $exception) {
            $this->isOnline = false;
            $this->statusCode = null;
            $this->responseTime = null;
            $this->error = $exception->getMessage();
        }

        $this->checkSsl();

        $this->checked = true;
    }

    private function checkSsl(): void
    {
        $parsed = parse_url($this->url);

        if (($parsed['scheme'] ?? '') !== 'https') {
            return;
        }

        $host = $parsed['host'] ?? '';

        if ($host === '') {
            return;
        }

        try {
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ],
            ]);

            $stream = stream_socket_client(
                sprintf('ssl://%s:443', $host),
                $errno,
                $errstr,
                10,
                STREAM_CLIENT_CONNECT,
                $context,
            );

            if (! $stream) {
                $this->sslValid = false;

                return;
            }

            $params = stream_context_get_params($stream);

            /** @var OpenSSLCertificate $cert */
            $cert = $params['options']['ssl']['peer_certificate'];

            /** @var array{subject: array{CN: string}, issuer: array{CN: string, O: string}, validTo_time_t: int} $certInfo */
            $certInfo = openssl_x509_parse($cert);

            $expiresAt = $certInfo['validTo_time_t'];
            $this->sslDaysLeft = (int) ceil(($expiresAt - time()) / 86400);
            $this->sslValid = $this->sslDaysLeft > 0;
            $this->sslIssuer = $certInfo['issuer']['O'] ?? $certInfo['issuer']['CN'] ?? 'Unknown';

            fclose($stream);
        } catch (Exception) {
            $this->sslValid = false;
        }
    }
}
