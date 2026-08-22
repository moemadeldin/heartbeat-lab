<?php

declare(strict_types=1);

namespace App\Providers;

use App\Interfaces\UrlValidator;
use App\Utilities\UrlValidator as UrlValidatorImpl;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UrlValidator::class, UrlValidatorImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::shouldBeStrict();

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        Validator::extend('url_or_domain', function (string $attribute, mixed $value): bool {
            if (! is_string($value)) {
                return false;
            }

            $value = mb_trim($value);

            if ($value === '') {
                return false;
            }

            if (! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://')) {
                $value = 'https://'.$value;
            }

            $parsed = parse_url($value);

            if ($parsed === false || ! isset($parsed['host'])) {
                return false;
            }

            $host = $parsed['host'];

            if ($host === '' || str_contains($host, ' ')) {
                return false;
            }

            // Host must contain a dot (e.g., example.com) or be an IP address
            return str_contains($host, '.') || filter_var($host, FILTER_VALIDATE_IP) !== false;
        }, 'The :attribute must be a valid URL or domain (e.g., example.com or https://example.com).');
    }
}
