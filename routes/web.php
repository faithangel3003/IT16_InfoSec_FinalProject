<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\InventoryReportsController;
use App\Http\Controllers\RoomDashboardController;
use App\Http\Controllers\InventoryDashboardController;
use App\Http\Controllers\ItemRequestController;

//-----------------------------------------------------------------------------------------------

// Default Route
Route::get('/', function () {
    if (Auth::check()) {
        $role = auth()->user()->role;
        if ($role === 'room_manager') {
            return redirect()->route('room.dashboard');
        } elseif ($role === 'inventory_manager') {
            return redirect()->route('inventory.dashboard');
        } elseif ($role === 'security') {
            return redirect()->route('security.dashboard');
        }
        return redirect()->route('dashboard');
    }
    return redirect()->route('login'); 
})->name('home');



Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/captcha/refresh', [\App\Http\Controllers\CaptchaController::class, 'refresh'])->name('captcha.refresh');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'verifyEmail'])->name('password.verify-email');
Route::get('/verify-security', function() {
    return redirect()->route('password.forgot')->with('info', 'Please enter your email to verify your identity.');
});
Route::post('/verify-security', [\App\Http\Controllers\PasswordResetController::class, 'verifySecurityAnswer'])->name('password.verify-security');
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.reset');
Route::post('/api/check-email', [\App\Http\Controllers\PasswordResetController::class, 'checkEmail'])->name('api.check-email');

//-----------------------------------------------------------------------------------------------

Route::middleware('auth')->group(function () {
    
    // Supplier - Inventory Manager & Admin access
    Route::middleware('role:inventory_manager')->group(function () {
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });
    
    // Inventory - Inventory Manager & Admin access
    Route::middleware('role:inventory_manager')->group(function () {
        Route::get('/inventory', [ItemController::class, 'index'])->name('inventory.index');
        Route::post('/inventory', [ItemController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/{id}/edit', [ItemController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{id}', [ItemController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{id}', [ItemController::class, 'destroy'])->name('inventory.destroy');
        Route::get('/inventory/item-categories', [ItemCategoryController::class, 'index'])->name('inventory.itemctgry');
        Route::post('/inventory/item-categories', [ItemCategoryController::class, 'store'])->name('inventory.itemctgry.store');
        Route::get('/inventory/item-categories/{id}/edit', [ItemCategoryController::class, 'edit'])->name('inventory.itemctgryedit');
        Route::put('/inventory/item-categories/{id}', [ItemCategoryController::class, 'update'])->name('inventory.itemctgry.update');
        Route::delete('/inventory/item-categories/{id}', [ItemCategoryController::class, 'destroy'])->name('inventory.itemctgry.destroy');
    });

    // Stock In - Inventory Manager & Admin access
    Route::middleware('role:inventory_manager')->group(function () {
        Route::get('/stock_in', [StockInController::class, 'index'])->name('stock_in.index');
        Route::get('/stock_in/create', [StockInController::class, 'create'])->name('stock_in.create');
        Route::post('/stock_in', [StockInController::class, 'store'])->name('stock_in.store');
        Route::get('/stock_in/{id}/edit', [StockInController::class, 'edit'])->name('stock_in.edit');
        Route::put('/stock_in/{id}', [StockInController::class, 'update'])->name('stock_in.update');
        Route::delete('/stock_in/{id}', [StockInController::class, 'destroy'])->name('stock_in.destroy');
    });

    // Stock Out - Inventory Manager & Admin access
    Route::middleware('role:inventory_manager')->group(function () {
        Route::get('/stock-out', [StockOutController::class, 'index'])->name('stock_out.index');
        Route::post('/stock-out/finalize', [StockOutController::class, 'finalize'])->name('stock_out.finalize');
        Route::post('/inventory/stock-out/{id}', [StockOutController::class, 'addToStockOut'])->name('stock_out.add');
    });

    // Rooms - Room Manager & Admin access
    Route::middleware('role:room_manager')->group(function () {
        Route::get('/rooms', [RoomsController::class, 'index'])->name('rooms.index');
        Route::post('/rooms', [RoomsController::class, 'store'])->name('rooms.store');
        Route::post('/rooms/{id}/assign', [RoomsController::class, 'assignItems'])->name('rooms.assign');
        Route::get('/rooms/{id}/edit', [RoomsController::class, 'edit'])->name('rooms.edit');
        Route::put('/rooms/{id}', [RoomsController::class, 'update'])->name('rooms.update');
        Route::delete('/rooms/{id}', [RoomsController::class, 'destroy'])->name('rooms.destroy');
        Route::get('/rooms/{id}/view', [RoomsController::class, 'view'])->name('rooms.view');
        Route::get('/rooms/type', [RoomTypeController::class, 'index'])->name('rooms.type');
        Route::post('/rooms/type', [RoomTypeController::class, 'store'])->name('rooms.type.store');
        Route::get('/rooms/type/{id}/edit', [RoomTypeController::class, 'edit'])->name('rooms.type.edit');
        Route::put('/rooms/type/{id}', [RoomTypeController::class, 'update'])->name('rooms.type.update');
        Route::delete('/rooms/type/{id}', [RoomTypeController::class, 'destroy'])->name('rooms.type.destroy');
        Route::post('/rooms/{id}/return-item', [RoomsController::class, 'returnItem'])->name('rooms.returnItem');
        Route::post('/rooms/{id}/checkin', [RoomsController::class, 'checkIn'])->name('rooms.checkin');
        Route::post('/rooms/{id}/checkout', [RoomsController::class, 'checkOut'])->name('rooms.checkout');
        
        // Item Requests - Room Manager can create and view their requests
        Route::get('/item-requests', [ItemRequestController::class, 'index'])->name('item-requests.index');
        Route::post('/item-requests', [ItemRequestController::class, 'store'])->name('item-requests.store');
        Route::delete('/item-requests/{id}', [ItemRequestController::class, 'cancel'])->name('item-requests.cancel');
        Route::get('/item-requests/stock/{itemId}', [ItemRequestController::class, 'getItemStock'])->name('item-requests.stock');
    });
    
    // Profile - Any authenticated user
    Route::get('/profile', [AuthController::class, 'viewProfile'])->name('profile.view');
});

//-----------------------------------------------------------------------------------------------

// Role-Based Dashboard Routes
Route::middleware('auth')->group(function () {

    // Admin Dashboard - Admin Only
    Route::get('/dashboard', [\App\Http\Controllers\MainController::class, 'index'])
        ->middleware('role:admin')
        ->name('dashboard');

    // Room Manager Dashboard
    Route::get('/room-dashboard', [RoomDashboardController::class, 'index'])
        ->middleware('role:room_manager')
        ->name('room.dashboard');

    // Inventory Manager Dashboard
    Route::get('/inventory-dashboard', [InventoryDashboardController::class, 'index'])
        ->middleware('role:inventory_manager')
        ->name('inventory.dashboard');

    // Security Dashboard
    Route::get('/security-dashboard', [\App\Http\Controllers\SecurityDashboardController::class, 'index'])
        ->middleware('role:security')
        ->name('security.dashboard');
});

//-----------------------------------------------------------------------------------------------

// Admin-Only Routes
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Audit Logs
    Route::get('/reports', [\App\Http\Controllers\ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [\App\Http\Controllers\ReportsController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export-csv', [\App\Http\Controllers\ReportsController::class, 'exportCsv'])->name('reports.export.csv');

    // Employees Management
    Route::get('/employees', [\App\Http\Controllers\EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', fn() => view('employees.create'))->name('employees.create');
    Route::post('/employees', [\App\Http\Controllers\EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}/edit', [\App\Http\Controllers\EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [\App\Http\Controllers\EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Archive Management (Admin only in profile)
    Route::get('/archives', [\App\Http\Controllers\ArchiveController::class, 'index'])->name('archives.index');
    Route::get('/archives/{id}', [\App\Http\Controllers\ArchiveController::class, 'show'])->name('archives.show');
});

//-----------------------------------------------------------------------------------------------

// Security Role Routes (Security Dashboard + Incidents + System Logs)
Route::middleware(['auth', 'role:security'])->group(function () {
    // Security Dashboard & Features
    Route::get('/security', [\App\Http\Controllers\SecurityController::class, 'index'])->name('security.index');
    Route::get('/security/activity-logs', [\App\Http\Controllers\SecurityController::class, 'activityLogs'])->name('security.activity-logs');
    Route::get('/security/unmask-logs', [\App\Http\Controllers\SecurityController::class, 'unmaskLogs'])->name('security.unmask-logs');

    // Incidents Management
    Route::get('/incidents', [\App\Http\Controllers\IncidentController::class, 'index'])->name('incidents.index');
    Route::post('/incidents', [\App\Http\Controllers\IncidentController::class, 'store'])->name('incidents.store');
    Route::put('/incidents/{id}/status', [\App\Http\Controllers\IncidentController::class, 'updateStatus'])->name('incidents.updateStatus');
    Route::get('/incidents/report', [\App\Http\Controllers\IncidentController::class, 'report'])->name('incidents.report');
    Route::get('/incidents/blocklist', [\App\Http\Controllers\IncidentController::class, 'blocklist'])->name('incidents.blocklist');
    Route::post('/incidents/blocklist', [\App\Http\Controllers\IncidentController::class, 'blockIp'])->name('incidents.block');
    Route::delete('/incidents/blocklist/{id}', [\App\Http\Controllers\IncidentController::class, 'unblockIp'])->name('incidents.unblock');

    // System Logs
    Route::get('/system-logs', [\App\Http\Controllers\SystemLogController::class, 'index'])->name('system-logs.index');
    Route::get('/system-logs/export', [\App\Http\Controllers\SystemLogController::class, 'exportCsv'])->name('system-logs.export');
    Route::get('/system-logs/{id}', [\App\Http\Controllers\SystemLogController::class, 'show'])->name('system-logs.show');
});

//-----------------------------------------------------------------------------------------------

// Security API Routes - Any authenticated user (for data masking in their accessible areas)
Route::middleware('auth')->group(function () {
    Route::post('/security/verify-credentials', [\App\Http\Controllers\SecurityController::class, 'verifyCredentials'])->name('security.verify-credentials');
    Route::post('/security/check-verification', [\App\Http\Controllers\SecurityController::class, 'checkVerification'])->name('security.check-verification');
    Route::post('/security/unmask-data', [\App\Http\Controllers\SecurityController::class, 'unmaskData'])->name('security.unmask-data');
    
    // Incident Reporting - Any authenticated user can report incidents
    Route::post('/report-incident', [\App\Http\Controllers\IncidentController::class, 'reportIncident'])->name('incidents.report-incident');
    
    // Password Change with Authentication
    Route::post('/profile/verify-password', [\App\Http\Controllers\AuthController::class, 'verifyCurrentPassword'])->name('profile.verify-password');
    Route::post('/profile/change-password', [\App\Http\Controllers\AuthController::class, 'changePassword'])->name('profile.change-password');
    Route::post('/profile/security-question', [\App\Http\Controllers\AuthController::class, 'setSecurityQuestion'])->name('profile.security-question');
});

//-----------------------------------------------------------------------------------------------

// Inventory Reports - Inventory Manager & Admin access  
Route::middleware(['auth', 'role:inventory_manager'])->group(function () {
    Route::get('/inventory-reports', [\App\Http\Controllers\InventoryReportsController::class, 'index'])->name('inventory.reports');
    Route::get('/inventory-reports/pdf', [\App\Http\Controllers\InventoryReportsController::class, 'exportPdf'])->name('inventory.reports.pdf');
    
    // Item Requests Management - Inventory Manager can approve, reject, fulfill requests
    Route::get('/item-requests/manage', [ItemRequestController::class, 'manageRequests'])->name('item-requests.manage');
    Route::post('/item-requests/{id}/approve', [ItemRequestController::class, 'approve'])->name('item-requests.approve');
    Route::post('/item-requests/{id}/reject', [ItemRequestController::class, 'reject'])->name('item-requests.reject');
    Route::post('/item-requests/{id}/fulfill', [ItemRequestController::class, 'fulfill'])->name('item-requests.fulfill');
});