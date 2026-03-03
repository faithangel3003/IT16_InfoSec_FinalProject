<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'itemctgry_id', 'name',
    ];

    protected $primaryKey = 'itemctgry_id';
    public $incrementing = false;
    protected $keyType = 'string';

    public static $rules = [
        'name' => 'required|string|max:255|unique:item_categories,name',
    ];

    protected static function booted()
    {
        static::creating(function ($itemCategory) {
            $lastCategory = ItemCategory::orderBy('itemctgry_id', 'desc')->first();
            $lastId = $lastCategory ? (int) substr($lastCategory->itemctgry_id, 2) : 0;
            $newId = 'IC' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
            $itemCategory->itemctgry_id = $newId;
        });
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id', 'itemctgry_id');
    }
}