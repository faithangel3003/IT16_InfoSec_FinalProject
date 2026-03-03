<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('User Model', function () {

    it('creates a user with required fields', function () {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        expect($user)->toBeInstanceOf(User::class);
        expect($user->name)->toBe('Test User');
        expect($user->email)->toBe('test@example.com');
        expect($user->role)->toBe('employee');
    });

    it('hides sensitive attributes in array', function () {
        $user = User::factory()->create();
        
        $array = $user->toArray();
        
        expect($array)->not->toHaveKey('password');
        expect($array)->not->toHaveKey('remember_token');
    });

    it('checks if user is admin correctly', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        expect($admin->isAdmin())->toBeTrue();
        expect($employee->isAdmin())->toBeFalse();
    });

    it('checks if user is security correctly', function () {
        $security = User::factory()->create(['role' => 'security']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        expect($security->isSecurity())->toBeTrue();
        expect($employee->isSecurity())->toBeFalse();
    });

    it('checks if user has security access correctly', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        $security = User::factory()->create(['role' => 'security']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        expect($admin->hasSecurityAccess())->toBeTrue();
        expect($security->hasSecurityAccess())->toBeTrue();
        expect($employee->hasSecurityAccess())->toBeFalse();
    });

    it('identifies super admin correctly', function () {
        $superAdmin = User::factory()->create(['role' => 'super_admin']);
        $admin = User::factory()->create(['role' => 'admin']);
        
        expect($superAdmin->isSuperAdmin())->toBeTrue();
        expect($admin->isSuperAdmin())->toBeFalse();
    });

    it('identifies inventory manager correctly', function () {
        $manager = User::factory()->create(['role' => 'inventory_manager']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        expect($manager->isInventoryManager())->toBeTrue();
        expect($employee->isInventoryManager())->toBeFalse();
    });

    it('identifies room manager correctly', function () {
        $manager = User::factory()->create(['role' => 'room_manager']);
        $employee = User::factory()->create(['role' => 'employee']);
        
        expect($manager->isRoomManager())->toBeTrue();
        expect($employee->isRoomManager())->toBeFalse();
    });

    it('checks locked status correctly', function () {
        $lockedUser = User::factory()->create(['is_locked' => true]);
        $activeUser = User::factory()->create(['is_locked' => false]);
        
        expect($lockedUser->is_locked)->toBeTrue();
        expect($activeUser->is_locked)->toBeFalse();
    });

    it('casts email_verified_at to datetime', function () {
        $user = User::factory()->create([
            'email_verified_at' => now()
        ]);
        
        expect($user->email_verified_at)->toBeInstanceOf(\Carbon\Carbon::class);
    });

});

describe('User Relationships', function () {

    it('has many login logs', function () {
        $user = User::factory()->create();
        
        expect($user->loginLogs())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
    });

});
