<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Archive extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'entity_name',
        'entity_data',
        'deletion_reason',
        'deleted_by',
        'deleted_by_name',
        'deleted_by_role',
        'deleted_at',
    ];

    protected $casts = [
        'entity_data' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who deleted this entity
     */
    public function deletedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Alias for deletedByUser relationship
     */
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get formatted entity type name
     */
    public function getEntityTypeDisplayAttribute(): string
    {
        return match($this->entity_type) {
            'supplier' => 'Supplier',
            'employee' => 'Employee',
            'room' => 'Room',
            'room_type' => 'Room Type',
            'item' => 'Item',
            'item_category' => 'Item Category',
            'stock_in' => 'Stock In',
            'stock_out' => 'Stock Out',
            'item_request' => 'Item Request',
            'user' => 'User',
            default => ucfirst(str_replace('_', ' ', $this->entity_type)),
        };
    }

    /**
     * Get icon for entity type
     */
    public function getEntityIconAttribute(): string
    {
        return match($this->entity_type) {
            'supplier' => 'person-fill-down',
            'employee' => 'people-fill',
            'room' => 'door-open-fill',
            'room_type' => 'building',
            'item' => 'box-fill',
            'item_category' => 'tags-fill',
            'stock_in' => 'box-arrow-in-down',
            'stock_out' => 'box-arrow-up',
            'item_request' => 'clipboard-check',
            'user' => 'person-fill',
            default => 'archive-fill',
        };
    }

    /**
     * Create an archive entry for a deleted entity
     */
    public static function archiveEntity($entity, string $entityType, string $reason): self
    {
        $user = auth()->user();
        
        // Get entity name based on type
        $entityName = match(true) {
            isset($entity->name) => $entity->name,
            isset($entity->first_name) => $entity->first_name . ' ' . ($entity->last_name ?? ''),
            isset($entity->room_number) => $entity->room_number,
            isset($entity->type_name) => $entity->type_name,
            isset($entity->category_name) => $entity->category_name,
            default => "#{$entity->id}",
        };

        return self::create([
            'entity_type' => $entityType,
            'entity_id' => $entity->id,
            'entity_name' => $entityName,
            'entity_data' => $entity->toArray(),
            'deletion_reason' => $reason,
            'deleted_by' => $user->id,
            'deleted_by_name' => $user->name,
            'deleted_by_role' => $user->role,
            'deleted_at' => now(),
        ]);
    }
}
