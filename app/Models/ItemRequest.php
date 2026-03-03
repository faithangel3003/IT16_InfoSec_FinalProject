<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'room_id',
        'item_id',
        'quantity',
        'status',
        'requested_by',
        'processed_by',
        'notes',
        'rejection_reason',
        'requested_at',
        'processed_at',
    ];

    protected $primaryKey = 'request_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public static $rules = [
        'room_id' => 'required|exists:rooms,room_id',
        'item_id' => 'required|exists:items,item_id',
        'quantity' => 'required|integer|min:1',
        'notes' => 'nullable|string|max:500',
    ];

    protected static function booted()
    {
        static::creating(function ($request) {
            $lastRequest = ItemRequest::orderBy('request_id', 'desc')->first();
            $lastId = $lastRequest ? (int) substr($lastRequest->request_id, 3) : 0;
            $request->request_id = 'REQ' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
            $request->requested_at = now();
        });
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id', 'room_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'item_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by', 'id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by', 'id');
    }

    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-info',
            'rejected' => 'badge-danger',
            'fulfilled' => 'badge-success',
            default => 'badge-secondary',
        };
    }
}
