<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ============================================
// PUBLIC API ROUTES
// ============================================

// Mobile Login (Public)
Route::post('/mobile-login', [App\Http\Controllers\Api\Auth\MobileLoginController::class, 'login'])->name('api.mobile.login');

// ============================================
// STAFF API ROUTES (Sanctum Authentication)
// ============================================

Route::prefix('staff')->name('api.staff.')->group(function () {
    // Public staff routes (no authentication)
    Route::post('/login', [App\Http\Controllers\Api\Staff\AuthController::class, 'login'])->name('login');
    
    // Protected staff routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication
        Route::post('/logout', [App\Http\Controllers\Api\Staff\AuthController::class, 'logout'])->name('logout');
        Route::get('/profile', [App\Http\Controllers\Api\Staff\AuthController::class, 'profile'])->name('profile');
        Route::post('/refresh-token', [App\Http\Controllers\Api\Staff\AuthController::class, 'refresh'])->name('refresh');
        
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Api\Staff\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/performance', [App\Http\Controllers\Api\Staff\DashboardController::class, 'performance'])->name('performance');
        
        // Tasks
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\Staff\TaskController::class, 'index'])->name('index');
            Route::get('/{uuid}', [App\Http\Controllers\Api\Staff\TaskController::class, 'show'])->name('show');
            Route::post('/{uuid}/start', [App\Http\Controllers\Api\Staff\TaskController::class, 'start'])->name('start');
            Route::post('/{uuid}/complete', [App\Http\Controllers\Api\Staff\TaskController::class, 'complete'])->name('complete');
            Route::post('/{uuid}/upload-proof', [App\Http\Controllers\Api\Staff\TaskController::class, 'uploadProof'])->name('upload-proof');
        });
        
        // Attendance
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\Staff\AttendanceController::class, 'index'])->name('index');
            Route::get('/today', [App\Http\Controllers\Api\Staff\AttendanceController::class, 'today'])->name('today');
            Route::post('/check-in', [App\Http\Controllers\Api\Staff\AttendanceController::class, 'checkIn'])->name('check-in');
            Route::post('/check-out', [App\Http\Controllers\Api\Staff\AttendanceController::class, 'checkOut'])->name('check-out');
        });
        
        // Leave Requests
        Route::prefix('leave-requests')->name('leave-requests.')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\Staff\AttendanceController::class, 'leaveRequests'])->name('index');
            Route::post('/', [App\Http\Controllers\Api\Staff\AttendanceController::class, 'submitLeaveRequest'])->name('store');
        });
    });
});

// ============================================
// OWNER API ROUTES (Sanctum Authentication)
// ============================================

Route::prefix('owner')->name('api.owner.')->middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Api\Owner\DashboardController::class, 'index'])->name('dashboard');

    // Finance — Income CRUD
    Route::get('/finance/summary', [App\Http\Controllers\Api\Owner\FinanceController::class, 'summary'])->name('finance.summary');
    Route::apiResource('finance', App\Http\Controllers\Api\Owner\FinanceController::class);

    // Finance — Expense CRUD
    Route::get('/expense-categories', [App\Http\Controllers\Api\Owner\ExpenseController::class, 'categories'])->name('expense-categories');
    Route::apiResource('expenses', App\Http\Controllers\Api\Owner\ExpenseController::class)->except(['show']);
    
    // Profile
    Route::get('/profile', [App\Http\Controllers\Api\Owner\ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [App\Http\Controllers\Api\Owner\ProfileController::class, 'update'])->name('profile.update');
    
    // Bookings
    // Bookings
    Route::get('/bookings/counts', [App\Http\Controllers\Api\Owner\BookingController::class, 'counts'])->name('bookings.counts');
    Route::post('bookings/{id}/check-in', [\App\Http\Controllers\Api\Owner\CheckInController::class, 'store']);
    Route::post('bookings/{id}/check-out', [\App\Http\Controllers\Api\Owner\CheckOutController::class, 'store']);
    Route::patch('/bookings/{id}/status', [App\Http\Controllers\Api\Owner\BookingController::class, 'updateStatus'])->name('bookings.status');
    Route::get('/bookings/{id}/invoice', [App\Http\Controllers\Api\Owner\BookingController::class, 'downloadInvoice'])->name('bookings.invoice');
    Route::patch('/bookings/{id}/payment', [App\Http\Controllers\Api\Owner\BookingController::class, 'updatePayment'])->name('bookings.payment');
    Route::post('/bookings/{id}/refund', [App\Http\Controllers\Api\Owner\BookingController::class, 'recordRefund'])->name('bookings.refund');
    Route::apiResource('bookings', \App\Http\Controllers\Api\Owner\BookingController::class);
    
    // Properties
    Route::get('/properties/{id}', [App\Http\Controllers\Api\Owner\PropertyController::class, 'show'])->name('properties.show');
    Route::get('/properties', [App\Http\Controllers\Api\Owner\PropertyController::class, 'index'])->name('properties.index');
    Route::put('/properties/{id}', [App\Http\Controllers\Api\Owner\PropertyController::class, 'update'])->name('properties.update');
    Route::patch('/properties/{id}/status', [App\Http\Controllers\Api\Owner\PropertyController::class, 'toggleStatus'])->name('properties.status');
    Route::post('/properties/{id}/photos', [App\Http\Controllers\Api\Owner\PropertyController::class, 'storePhotos'])->name('properties.photos.store');
    Route::delete('/properties/{id}/photos/{photoId}', [App\Http\Controllers\Api\Owner\PropertyController::class, 'deletePhoto'])->name('properties.photos.delete');
    Route::post('/properties', [App\Http\Controllers\Api\Owner\PropertyController::class, 'store'])->name('properties.store');
    Route::post('/properties/{id}/accommodations', [App\Http\Controllers\Api\Owner\PropertyController::class, 'storeAccommodation'])->name('properties.accommodations.store');
    Route::get('/property-categories', [App\Http\Controllers\Api\Owner\PropertyController::class, 'categories'])->name('properties.categories');
    Route::get('/predefined-accommodation-types', [App\Http\Controllers\Api\Owner\PropertyController::class, 'predefinedTypes'])->name('properties.predefinedTypes');
    Route::get('/amenities', [App\Http\Controllers\Api\Owner\PropertyController::class, 'amenities'])->name('properties.amenities');

    // Accommodation Photos
    Route::post('/properties/{id}/accommodations/{accommodationId}/photos', [App\Http\Controllers\Api\Owner\PropertyController::class, 'storeAccommodationPhotos'])->name('properties.accommodations.photos.store');
    Route::delete('/properties/{id}/accommodations/{accommodationId}/photos/{photoId}', [App\Http\Controllers\Api\Owner\PropertyController::class, 'deleteAccommodationPhoto'])->name('properties.accommodations.photos.delete');

    // B2B Partners
    Route::get('/b2b', [App\Http\Controllers\Api\Owner\B2bController::class, 'index'])->name('b2b.index');
    Route::post('/b2b', [App\Http\Controllers\Api\Owner\B2bController::class, 'store'])->name('b2b.store');
    Route::put('/b2b/{id}', [App\Http\Controllers\Api\Owner\B2bController::class, 'update'])->name('b2b.update');

    // Guests
    Route::get('/guests', [App\Http\Controllers\Api\Owner\GuestController::class, 'index'])->name('guests.index');
    Route::post('/guests', [App\Http\Controllers\Api\Owner\GuestController::class, 'store'])->name('guests.store');
    // Check-in/Check-out History
    Route::get('/checkins', [App\Http\Controllers\Api\Owner\CheckInController::class, 'index'])->name('checkins.index');
    Route::get('/checkins/{uuid}', [App\Http\Controllers\Api\Owner\CheckInController::class, 'show'])->name('checkins.show');
    
    Route::get('/checkouts', [App\Http\Controllers\Api\Owner\CheckOutController::class, 'index'])->name('checkouts.index');
    Route::get('/checkouts/{uuid}', [App\Http\Controllers\Api\Owner\CheckOutController::class, 'show'])->name('checkouts.show');
    Route::put('/guests/{id}', [App\Http\Controllers\Api\Owner\GuestController::class, 'update'])->name('guests.update');

    // Staff Management
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [App\Http\Controllers\Api\Owner\StaffManagementController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Api\Owner\StaffManagementController::class, 'store'])->name('store');
        Route::get('/{uuid}', [App\Http\Controllers\Api\Owner\StaffManagementController::class, 'show'])->name('show');
        Route::put('/{uuid}', [App\Http\Controllers\Api\Owner\StaffManagementController::class, 'update'])->name('update');
        Route::delete('/{uuid}', [App\Http\Controllers\Api\Owner\StaffManagementController::class, 'destroy'])->name('destroy');
    });
    
    // Staff Hierarchy
    Route::get('/properties/{propertyId}/hierarchy', [App\Http\Controllers\Api\Owner\StaffManagementController::class, 'hierarchy'])->name('hierarchy');
    
    // Departments
    Route::get('/departments', [App\Http\Controllers\Api\Owner\StaffManagementController::class, 'departments'])->name('departments');
});

// ============================================
// LEGACY WEB API ROUTES (Session Authentication)
// ============================================

Route::middleware('auth:web')->group(function () {
    // Properties API routes
    Route::get('/properties', function () {
        return auth()->user()->properties()
            ->with(['category', 'location.city.district.state'])
            ->where('status', 'active')
            ->get()
            ->map(function ($property) {
                return [
                    'id' => $property->id,
                    'uuid' => $property->uuid,
                    'name' => $property->name,
                    'property_accommodations_count' => $property->propertyAccommodations()->count(),
                    'location' => $property->location,
                    'category' => $property->category,
                ];
            });
    });
    
    Route::get('/properties/{id}/accommodations', function ($id) {
        $property = auth()->user()->properties()->findOrFail($id);
        return $property->propertyAccommodations()
            ->with('predefinedType')
            ->get()
            ->map(function ($accommodation) {
                return [
                    'id' => $accommodation->id,
                    'uuid' => $accommodation->uuid,
                    'display_name' => $accommodation->display_name,
                    'base_price' => $accommodation->base_price,
                    'max_occupancy' => $accommodation->max_occupancy,
                    'predefined_type' => $accommodation->predefinedType,
                ];
            });
    });
    
    // B2B Partners API route
    Route::get('/partners', function () {
        return auth()->user()->b2bPartners()
            ->where('status', 'active')
            ->get()
            ->map(function ($partner) {
                return [
                    'id' => $partner->id,
                    'uuid' => $partner->uuid,
                    'partner_name' => $partner->partner_name,
                    'commission_rate' => $partner->commission_rate,
                    'partner_type' => $partner->partner_type,
                ];
            });
    });
    
    // Roles API route
    Route::get('/roles', function () {
        $roles = \App\Models\Role::select('name')
            ->distinct()
            ->orderBy('name')
            ->get()
            ->map(function ($role, $index) {
                return [
                    'id' => $index + 1,
                    'name' => $role->name
                ];
            });
        return response()->json(['roles' => $roles]);
    });
});
