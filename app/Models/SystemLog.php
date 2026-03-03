<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    protected $fillable = [
        'channel',
        'level',
        'action',
        'message',
        'user_id',
        'user_name',
        'ip_address',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getChannelColorAttribute(): string
    {
        return match($this->channel) {
            'audit' => '#c8a858',
            'security' => '#dc3545',
            'system' => '#17a2b8',
            'error' => '#fd7e14',
            default => '#6c757d',
        };
    }

    public function getLevelColorAttribute(): string
    {
        return match($this->level) {
            'info' => '#17a2b8',
            'warning' => '#ffc107',
            'error' => '#fd7e14',
            'critical' => '#dc3545',
            default => '#6c757d',
        };
    }

    public static function log(
        string $action,
        string $message,
        string $channel = 'audit',
        string $level = 'info',
        ?int $userId = null,
        ?string $userName = null,
        ?string $ipAddress = null,
        ?array $context = null
    ): self {
        return self::create([
            'channel' => $channel,
            'level' => $level,
            'action' => $action,
            'message' => $message,
            'user_id' => $userId ?? auth()->id(),
            'user_name' => $userName ?? auth()->user()?->name,
            'ip_address' => $ipAddress ?? request()->ip(),
            'context' => $context,
        ]);
    }
}
