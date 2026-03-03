<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetRequest extends Model
{
    protected $fillable = [
        'email',
        'token',
        'security_question',
        'security_answer_hash',
        'verified',
        'attempts',
        'expires_at',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'expires_at' => 'datetime',
    ];

    /**
     * Generate a new token
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Check if request is expired
     */
    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->expires_at);
    }

    /**
     * Check if too many attempts
     */
    public function hasTooManyAttempts(): bool
    {
        return $this->attempts >= 5;
    }

    /**
     * Increment attempts
     */
    public function incrementAttempts(): void
    {
        $this->attempts++;
        $this->save();
    }

    /**
     * Mark as verified
     */
    public function markVerified(): void
    {
        $this->verified = true;
        $this->save();
    }

    /**
     * Get available security questions
     */
    public static function getSecurityQuestions(): array
    {
        return [
            'pet' => 'What was the name of your first pet?',
            'school' => 'What elementary school did you attend?',
            'city' => 'In what city were you born?',
            'book' => 'What is your favorite book?',
            'food' => 'What is your favorite food?',
            'mother' => "What is your mother's maiden name?",
        ];
    }
}
