<?php

declare(strict_types=1);

namespace App\Livewire;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Url;
use Livewire\Component;

final class PublicStatusShow extends Component
{
    #[Url(as: 'url')]
    public string $url = '';

    public ?bool $isOnline = null;

    public ?int $statusCode = null;

    public ?float $responseTime = null;

    public ?string $error = null;

    public ?string $sslValid = null;

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

        $this->performCheck();
    }

    public function render(): Factory|View
    {
        return view('livewire.public-status-show');
    }

    private function performCheck(): void
    {
        $startTime = microtime(true);

        try {
            /** @var Response $response */
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Heartbeat-Lab/1.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get($this->url);

            $this->responseTime = round((microtime(true) - $startTime) * 1000, 2);
            $this->statusCode = $response->status();
            $this->isOnline = $response->successful();
        } catch (Exception $exception) {
            $this->isOnline = false;
            $this->statusCode = null;
            $this->responseTime = null;
            $this->error = $exception->getMessage();
        }

        $this->checked = true;
    }
}
