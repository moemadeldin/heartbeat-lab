<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SiteStatus;
use Database\Factories\SiteFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $url
 * @property SiteStatus $status
 * @property Carbon|null $ssl_expires_at
 * @property string|null $ssl_issuer
 * @property bool|null $ssl_valid
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class Site extends Model
{
    /** @use HasFactory<SiteFactory> */
    use HasFactory;

    use HasUuids;

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<SiteCheck, $this>
     */
    public function checks(): HasMany
    {
        return $this->hasMany(SiteCheck::class);
    }

    /**
     * @param  Builder<Site>  $query
     */
    #[Scope()]
    protected function userSites(Builder $query, User $user): void
    {
        $query->where('user_id', $user->id);
    }

    /**
     * @param  Builder<Site>  $query
     * @return Builder<Site>
     */
    #[Scope()]
    protected function whereURLDuplicate(Builder $query, User $user, string $url): Builder
    {
        return $query->where('user_id', $user->id)
            ->where('url', $url);
    }

    /**
     * @param  Builder<Site>  $query
     * @return Builder<Site>
     */
    #[Scope()]
    protected function whereNameDuplicate(Builder $query, User $user, string $name): Builder
    {
        return $query->where('user_id', $user->id)
            ->where('name', $name);
    }

    /**
     * @param  Builder<Site>  $query
     * @return Builder<Site>
     */
    #[Scope()]
    protected function sitesWithNoDuplicates(Builder $query): Builder
    {
        return $query->select(['user_id', 'id', 'name', 'url', 'status', 'created_at'])
            ->distinct()
            ->with('user')
            ->orderBy('created_at');
    }

    /**
     * @return Attribute<string, never>
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            set: function (string $value): string {
                $value = mb_trim($value);

                if ($value === '') {
                    return $value;
                }

                if (! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://')) {
                    return 'https://'.$value;
                }

                return $value;
            },
        );
    }

    /**
     * @return Attribute<array{
     *     class: string,
     *     dot: string,
     *     text: string
     * }, never>
     */
    protected function sslBadge(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $daysLeft = $this->ssl_expires_at !== null
                    ? (int) now()->diffInDays($this->ssl_expires_at)
                    : null;

                if ($this->ssl_valid === true && $daysLeft !== null) {
                    return match (true) {
                        $daysLeft > 30 => [
                            'class' => 'bg-green-500/10 text-green-400 border border-green-500/20',
                            'dot' => 'bg-green-400',
                            'text' => $daysLeft > 365
                                ? round($daysLeft / 365, 1).'y'
                                : $daysLeft.'d',
                        ],

                        $daysLeft > 14 => [
                            'class' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                            'dot' => 'bg-yellow-400',
                            'text' => $daysLeft.'d',
                        ],

                        $daysLeft > 0 => [
                            'class' => 'bg-orange-500/10 text-orange-400 border border-orange-500/20',
                            'dot' => 'bg-orange-400',
                            'text' => $daysLeft.'d',
                        ],

                        default => [
                            'class' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                            'dot' => 'bg-red-400',
                            'text' => 'Expired',
                        ],
                    };
                }

                if ($this->ssl_valid === false) {
                    return [
                        'class' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                        'dot' => 'bg-red-400',
                        'text' => 'Expired',
                    ];
                }

                if (str_starts_with((string) $this->url, 'https://')) {
                    return [
                        'class' => 'bg-gray-500/10 text-gray-400 border border-gray-500/20',
                        'dot' => 'bg-gray-400',
                        'text' => 'Pending',
                    ];
                }

                return [
                    'class' => 'bg-gray-500/10 text-gray-400 border border-gray-500/20',
                    'dot' => 'bg-gray-400',
                    'text' => '&mdash;',
                ];
            },
        );
    }

    /**
     * @return Attribute<float, never>
     */
    protected function uptime(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $checks = DB::table('site_checks')
                    ->where('site_id', $this->id)
                    ->orderByDesc('id')
                    ->limit(100)
                    ->pluck('status');

                $total = $checks->count();

                if ($total === 0) {
                    return 0.0;
                }

                $onlineCount = $checks->filter(fn ($s): bool => $s === SiteStatus::Online->value)->count();

                return round(($onlineCount / $total) * 100, 2);
            },
        );
    }

    /**
     * @return Attribute<array{class: string, dot: string, text: string}, never>
     */
    protected function statusBadge(): Attribute
    {
        return Attribute::make(
            get: fn (): array => match ($this->status) {
                SiteStatus::Online => [
                    'class' => 'bg-green-500/10 text-green-400 border border-green-500/20',
                    'dot' => 'bg-green-400 animate-pulse',
                    'text' => 'ONLINE',
                ],
                SiteStatus::Offline => [
                    'class' => 'bg-red-500/10 text-red-400 border border-red-500/20',
                    'dot' => 'bg-red-400',
                    'text' => 'OFFLINE',
                ],
                SiteStatus::Checking => [
                    'class' => 'bg-yellow-500/10 text-yellow-400 border border-yellow-500/20',
                    'dot' => 'bg-yellow-400 animate-pulse',
                    'text' => 'CHECKING',
                ],
            },
        );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'string',
            'name' => 'string',
            'url' => 'string',
            'status' => SiteStatus::class,
            'ssl_expires_at' => 'datetime',
            'ssl_issuer' => 'string',
            'ssl_valid' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
