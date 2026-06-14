<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Jobs\CheckPublicStatusJob;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Status Checker Result')]
final class PublicStatusShow extends Component
{
    private const int ATTEMPTS_LIMIT_PER_IP = 10;

    private const int DECAY_SECONDS = 60;

    #[Url(as: 'url')]
    public string $url = '';

    public string $token = '';

    public bool $loading = true;

    public int $pollCount = 0;

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

        $key = 'status-check:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, self::ATTEMPTS_LIMIT_PER_IP)) {
            $this->error = 'Too many requests. Please wait.';
            $this->loading = false;
            $this->checked = true;

            return;
        }

        RateLimiter::hit($key, self::DECAY_SECONDS);

        $this->token = Str::uuid()->toString();

        dispatch(new CheckPublicStatusJob($this->url, $this->token));
    }

    public function pollResult(): void
    {
        if ($this->checked) {
            return;
        }

        $this->pollCount++;

        if ($this->pollCount > 15) {
            $this->error = 'The check timed out. Please try again.';
            $this->loading = false;
            $this->checked = true;

            return;
        }

        $cached = Redis::get(sprintf('public-check:%s', $this->token));

        if ($cached === null) {
            return;
        }

        /** @var array{
         *     is_online: bool,
         *     status_code: int|null,
         *     response_time: float|null,
         *     error: string|null,
         *     ssl_valid: bool|null,
         *     ssl_issuer: string|null,
         *     ssl_days_left: int|null,
         * } $data
         */
        $data = json_decode($cached, true);

        $this->isOnline = $data['is_online'];
        $this->statusCode = $data['status_code'];
        $this->responseTime = $data['response_time'];
        $this->error = $data['error'];
        $this->sslValid = $data['ssl_valid'];
        $this->sslIssuer = $data['ssl_issuer'];
        $this->sslDaysLeft = $data['ssl_days_left'];
        $this->checked = true;
        $this->loading = false;
    }

    public function render(): View
    {
        return view('livewire.public-status-show');
    }
}
