<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataUnmaskLog extends Model
{
    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'field_name',
        'ip_address',
        'user_agent',
        'unmasked_at',
    ];

    protected $casts = [
        'unmasked_at' => 'datetime',
    ];

    /**
     * Get the user that unmasked the data.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Log a data unmask action.
     */
    public static function logUnmask(
        int $userId,
        string $entityType,
        int $entityId,
        string $fieldName
    ): self {
        return self::create([
            'user_id' => $userId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'field_name' => $fieldName,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'unmasked_at' => now(),
        ]);
    }
}
