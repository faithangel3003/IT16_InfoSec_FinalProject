<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Maximum failed login attempts before lockout
    const MAX_FAILED_ATTEMPTS = 5;
    // Lockout duration in minutes
    const LOCKOUT_DURATION = 15;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'failed_login_attempts',
        'locked_until',
        'last_failed_login_at',
        'has_unnotified_failed_attempts',
        'password_changed_at',
        'last_login_at',
        'last_login_ip',
        'security_question',
        'security_answer_hash',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is security officer.
     */
    public function isSecurity(): bool
    {
        return $this->role === 'security';
    }

    /**
     * Check if user has security access (admin or security role).
     */
    public function hasSecurityAccess(): bool
    {
        return $this->isAdmin() || $this->isSecurity();
    }

    /**
     * Check if user account is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if user is inventory manager.
     */
    public function isInventoryManager(): bool
    {
        return $this->role === 'inventory_manager';
    }

    /**
     * Check if user is room manager.
     */
    public function isRoomManager(): bool
    {
        return $this->role === 'room_manager';
    }

    /**
     * Check if user is any type of employee.
     */
    public function isEmployee(): bool
    {
        return in_array($this->role, ['employee', 'inventory_manager', 'room_manager']);
    }

    /**
     * Check if user has access to inventory features.
     */
    public function hasInventoryAccess(): bool
    {
        return $this->isAdmin() || $this->role === 'inventory_manager' || $this->role === 'employee';
    }

    /**
     * Check if user has access to room features.
     */
    public function hasRoomAccess(): bool
    {
        return $this->isAdmin() || $this->role === 'room_manager' || $this->role === 'employee';
    }

    /**
     * Get the employee record associated with the user.
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'id');
    }

    /**
     * Get login logs for this user.
     */
    public function loginLogs()
    {
        return $this->hasMany(LoginLog::class);
    }

    /**
     * Check if the user account is currently locked.
     */
    public function isLocked(): bool
    {
        if ($this->locked_until === null) {
            return false;
        }
        return Carbon::parse($this->locked_until)->isFuture();
    }

    /**
     * Get remaining lockout time in minutes.
     */
    public function getRemainingLockoutMinutes(): int
    {
        if (!$this->isLocked()) {
            return 0;
        }
        return Carbon::now()->diffInMinutes(Carbon::parse($this->locked_until), false);
    }

    /**
     * Increment failed login attempts and lock account if threshold reached.
     */
    public function incrementFailedAttempts(): void
    {
        $this->failed_login_attempts++;
        $this->last_failed_login_at = now();
        $this->has_unnotified_failed_attempts = true;

        if ($this->failed_login_attempts >= self::MAX_FAILED_ATTEMPTS) {
            $this->locked_until = Carbon::now()->addMinutes(self::LOCKOUT_DURATION);
        }

        $this->save();
    }

    /**
     * Reset failed login attempts after successful login.
     */
    public function resetFailedAttempts(): void
    {
        $this->failed_login_attempts = 0;
        $this->locked_until = null;
        $this->save();
    }

    /**
     * Mark failed attempts as notified.
     */
    public function markFailedAttemptsNotified(): void
    {
        $this->has_unnotified_failed_attempts = false;
        $this->save();
    }

    /**
     * Get failed login attempts count for notification.
     */
    public function getRecentFailedAttempts(): int
    {
        return $this->failed_login_attempts;
    }

    /**
     * Update last login information.
     */
    public function updateLastLogin(string $ipAddress): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ]);
    }

    /**
     * Check if user is Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user has permission to manage users.
     */
    public function canManageUsers(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    /**
     * Check if user has permission to view sensitive data.
     */
    public function canViewSensitiveData(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    /**
     * Get credential verifications for this user.
     */
    public function credentialVerifications()
    {
        return $this->hasMany(CredentialVerification::class);
    }

    /**
     * Get data unmask logs for this user.
     */
    public function dataUnmaskLogs()
    {
        return $this->hasMany(DataUnmaskLog::class);
    }

    /**
     * Check if user has a security question set up.
     */
    public function hasSecurityQuestion(): bool
    {
        return !empty($this->security_question) && !empty($this->security_answer_hash);
    }

    /**
     * Verify the security answer against stored hash.
     */
    public function verifySecurityAnswer(string $answer): bool
    {
        if (!$this->hasSecurityQuestion()) {
            return false;
        }
        
        // Case-insensitive comparison with trimmed answer
        $normalizedAnswer = strtolower(trim($answer));
        return Hash::check($normalizedAnswer, $this->security_answer_hash);
    }

    /**
     * Set the security question and answer.
     */
    public function setSecurityQuestion(string $questionKey, string $answer): void
    {
        $normalizedAnswer = strtolower(trim($answer));
        $this->update([
            'security_question' => $questionKey,
            'security_answer_hash' => Hash::make($normalizedAnswer),
        ]);
    }

    /**
     * Get the security question text for this user.
     */
    public function getSecurityQuestionText(): ?string
    {
        if (!$this->security_question) {
            return null;
        }
        
        $questions = PasswordResetRequest::getSecurityQuestions();
        return $questions[$this->security_question] ?? null;
    }
}
