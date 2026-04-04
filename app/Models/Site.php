<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SiteFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $url
 * @property bool $is_online
 * @property int|null $status_code
 * @property float $uptime
 * @property float|null $response_time
 * @property Carbon|null $last_checked_at
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
        return $query->select(['user_id', 'id', 'name', 'url', 'is_online', 'uptime', 'created_at'])
            ->distinct()
            ->with('user')
            ->orderBy('created_at');
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
            'is_online' => 'boolean',
            'status_code' => 'integer',
            'uptime' => 'float',
            'response_time' => 'float',
            'last_checked_at' => 'datetime',
        ];
    }
}
