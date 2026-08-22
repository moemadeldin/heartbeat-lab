<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SiteStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $site_id
 * @property SiteStatus $status
 * @property int|null $status_code
 * @property float|null $response_time
 * @property Carbon|null $checked_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
final class SiteCheck extends Model
{
    use HasFactory;

    /**
     * @return BelongsTo<Site, $this>
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    protected function casts(): array
    {
        return [
            'site_id' => 'string',
            'status' => SiteStatus::class,
            'status_code' => 'integer',
            'response_time' => 'float',
            'checked_at' => 'datetime',
        ];
    }
}
