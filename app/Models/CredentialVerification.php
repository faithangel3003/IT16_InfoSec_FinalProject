<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CredentialVerification extends Model
{
    protected $fillable = [
        'user_id',
        'action_type',
        'action_description',
        'ip_address',
        'token',
        'verified',
        'verified_at',
        'expires_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // Verification valid for 5 minutes
    const VERIFICATION_DURATION = 5;

    /**
     * Get the user that owns this verification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if verification is still valid.
     */
    public function isValid(): bool
    {
        if (!$this->verified) {
            return false;
        }
        if ($this->expires_at === null) {
            return false;
        }
        return Carbon::parse($this->expires_at)->isFuture();
    }

    /**
     * Create a new verification request for an action.
     */
    public static function createForAction(int $userId, string $actionType, ?string $description = null): self
    {
        return self::create([
            'user_id' => $userId,
            'action_type' => $actionType,
            'action_description' => $description,
            'ip_address' => request()->ip(),
            'token' => bin2hex(random_bytes(32)),
            'verified' => true,
            'verified_at' => now(),
            'expires_at' => Carbon::now()->addMinutes(self::VERIFICATION_DURATION),
        ]);
    }

    /**
     * Mark verification as verified.
     */
    public function markVerified(): void
    {
        $this->update([
            'verified' => true,
            'verified_at' => now(),
            'expires_at' => Carbon::now()->addMinutes(self::VERIFICATION_DURATION),
        ]);
    }

    /**
     * Check if user has valid verification for an action type.
     */
    public static function hasValidVerification(int $userId, string $actionType): bool
    {
        return self::where('user_id', $userId)
            ->where('action_type', $actionType)
            ->where('verified', true)
            ->where('expires_at', '>', now())
            ->exists();
    }
}
