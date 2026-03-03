<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class IpBlocklist extends Model
{
    protected $table = 'ip_blocklist';

    protected $fillable = [
        'ip_address',
        'reason',
        'duration',
        'blocked_at',
        'expires_at',
        'blocked_by',
        'is_active',
    ];

    protected $casts = [
        'blocked_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function blocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }

    public function getDurationDisplayAttribute(): string
    {
        return match($this->duration) {
            '24_hours' => '24 hours',
            '7_days' => '7 days',
            '30_days' => '30 days',
            'permanent' => 'Permanent',
            default => $this->duration,
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->duration === 'permanent') {
            return false;
        }
        return $this->expires_at && $this->expires_at->isPast();
    }

    public static function calculateExpiresAt(string $duration): ?Carbon
    {
        return match($duration) {
            '24_hours' => Carbon::now()->addHours(24),
            '7_days' => Carbon::now()->addDays(7),
            '30_days' => Carbon::now()->addDays(30),
            'permanent' => null,
            default => Carbon::now()->addHours(24),
        };
    }

    public static function isBlocked(string $ip): bool
    {
        $block = self::where('ip_address', $ip)
            ->where('is_active', true)
            ->first();

        if (!$block) {
            return false;
        }

        if ($block->is_expired) {
            $block->update(['is_active' => false]);
            return false;
        }

        return true;
    }
}
