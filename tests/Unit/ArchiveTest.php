<?php

use App\Models\Archive;
use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Archive Model', function () {

    it('creates an archive record', function () {
        $user = User::factory()->create(['role' => 'admin']);
        
        $archive = Archive::create([
            'entity_type' => 'item',
            'entity_id' => 1,
            'entity_data' => ['name' => 'Test Item', 'quantity' => 100],
            'deletion_reason' => 'Testing archive',
            'deleted_by' => $user->id,
        ]);
        
        expect($archive)->toBeInstanceOf(Archive::class);
        expect($archive->entity_type)->toBe('item');
        expect($archive->entity_id)->toBe(1);
        expect($archive->deletion_reason)->toBe('Testing archive');
    });

    it('casts entity_data to array', function () {
        $user = User::factory()->create();
        
        $archive = Archive::create([
            'entity_type' => 'employee',
            'entity_id' => 5,
            'entity_data' => ['first_name' => 'John', 'last_name' => 'Doe'],
            'deleted_by' => $user->id,
        ]);
        
        expect($archive->entity_data)->toBeArray();
        expect($archive->entity_data['first_name'])->toBe('John');
    });

    it('returns correct entity type display name', function () {
        $archive = new Archive(['entity_type' => 'item']);
        expect($archive->entity_type_display)->toBe('Item');
        
        $archive = new Archive(['entity_type' => 'employee']);
        expect($archive->entity_type_display)->toBe('Employee');
    });

    it('returns correct entity icon', function () {
        $itemArchive = new Archive(['entity_type' => 'item']);
        expect($itemArchive->entity_icon)->toBe('box-seam');
        
        $employeeArchive = new Archive(['entity_type' => 'employee']);
        expect($employeeArchive->entity_icon)->toBe('person-badge');
        
        $userArchive = new Archive(['entity_type' => 'user']);
        expect($userArchive->entity_icon)->toBe('person-circle');
        
        $incidentArchive = new Archive(['entity_type' => 'incident']);
        expect($incidentArchive->entity_icon)->toBe('exclamation-triangle');
    });

    it('belongs to deleter user', function () {
        $user = User::factory()->create();
        
        $archive = Archive::create([
            'entity_type' => 'report',
            'entity_id' => 1,
            'entity_data' => ['title' => 'Monthly Report'],
            'deleted_by' => $user->id,
        ]);
        
        expect($archive->deleter)->toBeInstanceOf(User::class);
        expect($archive->deleter->id)->toBe($user->id);
    });

});

describe('Archive Statistics', function () {

    beforeEach(function () {
        $this->user = User::factory()->create(['role' => 'admin']);
    });

    it('counts total archives', function () {
        Archive::create([
            'entity_type' => 'item',
            'entity_id' => 1,
            'entity_data' => [],
            'deleted_by' => $this->user->id,
        ]);
        
        Archive::create([
            'entity_type' => 'employee',
            'entity_id' => 2,
            'entity_data' => [],
            'deleted_by' => $this->user->id,
        ]);
        
        expect(Archive::count())->toBe(2);
    });

    it('groups archives by type', function () {
        Archive::create([
            'entity_type' => 'item',
            'entity_id' => 1,
            'entity_data' => [],
            'deleted_by' => $this->user->id,
        ]);
        
        Archive::create([
            'entity_type' => 'item',
            'entity_id' => 2,
            'entity_data' => [],
            'deleted_by' => $this->user->id,
        ]);
        
        Archive::create([
            'entity_type' => 'employee',
            'entity_id' => 1,
            'entity_data' => [],
            'deleted_by' => $this->user->id,
        ]);
        
        $byType = Archive::selectRaw('entity_type, count(*) as count')
            ->groupBy('entity_type')
            ->pluck('count', 'entity_type');
        
        expect($byType['item'])->toBe(2);
        expect($byType['employee'])->toBe(1);
    });

});
