<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'ip_address',
        'user_agent',
        'status',
        'failure_reason',
        'login_at',
        'logout_at',
        'session_duration',
    ];

    protected $casts = [
        'login_at' => 'datetime',
        'logout_at' => 'datetime',
    ];

    /**
     * Get the user associated with the login log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get formatted session duration.
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->session_duration) {
            return 'N/A';
        }
        
        $hours = floor($this->session_duration / 3600);
        $minutes = floor(($this->session_duration % 3600) / 60);
        $seconds = $this->session_duration % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        }
        return sprintf('%ds', $seconds);
    }
}
