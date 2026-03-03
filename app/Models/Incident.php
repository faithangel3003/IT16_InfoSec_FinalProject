<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    protected $fillable = [
        'severity',
        'type',
        'description',
        'status',
        'ip_address',
        'user_id',
        'reported_by',
        'reported_by_name',
        'reported_by_role',
        'affected_resource',
        'resolution_notes',
        'detected_at',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function getSeverityColorAttribute(): string
    {
        return match($this->severity) {
            'critical' => '#dc3545',
            'high' => '#fd7e14',
            'medium' => '#ffc107',
            'low' => '#28a745',
            default => '#6c757d',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open' => '#dc3545',
            'investigating' => '#ffc107',
            'contained' => '#17a2b8',
            'resolved' => '#28a745',
            default => '#6c757d',
        };
    }

    public function getTypeDisplayAttribute(): string
    {
        return match($this->type) {
            'unauthorized_access' => 'Unauthorized Access',
            'brute_force' => 'Brute Force Attack',
            'suspicious_activity' => 'Suspicious Activity',
            'data_breach' => 'Data Breach',
            'system_error' => 'System Error',
            'policy_violation' => 'Policy Violation',
            'other' => 'Other',
            default => ucfirst($this->type),
        };
    }
}
