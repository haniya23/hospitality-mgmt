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

// Cashfree Webhook (must be public)
Route::post('/cashfree/webhook', [App\Http\Controllers\CashfreeController::class, 'webhook'])->name('cashfree.webhook');

// Subscription API routes
Route::middleware('auth:web')->group(function () {
    Route::post('/subscription/create-order', [App\Http\Controllers\SubscriptionController::class, 'createOrder']);
    Route::post('/subscription/addons', [App\Http\Controllers\SubscriptionController::class, 'addAccommodations']);
    Route::get('/subscription/status', [App\Http\Controllers\SubscriptionController::class, 'status']);
    
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
