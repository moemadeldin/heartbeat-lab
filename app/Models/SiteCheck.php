<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
/**
 * @property string $site_id
 * @property bool $is_online
 * @property int $status_code
 * @property float $response_time
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
            'is_online' => 'boolean',
            'status_code' => 'integer',
            'response_time' => 'float',
            'checked_at' => 'datetime',
        ];
    }
}
