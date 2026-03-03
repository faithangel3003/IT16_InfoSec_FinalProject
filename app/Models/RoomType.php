<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'roomtype_id', 'name',
    ];

    protected $primaryKey = 'roomtype_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public static $rules = [
        'name' => 'required|string|max:255|unique:room_types,name',
    ];

    protected static function booted()
    {
        static::creating(function ($roomType) {
            $lastRoomType = RoomType::orderBy('roomtype_id', 'desc')->first();
            $lastId = $lastRoomType ? (int) substr($lastRoomType->roomtype_id, 2) : 0;
            $newId = 'RT' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
            $roomType->roomtype_id = $newId;
        });
    }

    public function rooms()
    {
        return $this->hasMany(Room::class, 'roomtype_id', 'roomtype_id');
    }
}