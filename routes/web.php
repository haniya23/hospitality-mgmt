<?php

use App\Http\Controllers\Auth\MobileAuthController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [App\Http\Controllers\PublicController::class, 'simple'])->name('public.index');

// Staff Login Redirect
Route::get('/staff', function () {
    return redirect()->route('staff.login');
});

// Authentication Routes
Route::get('/login', [MobileAuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [MobileAuthController::class, 'login'])->middleware('guest');
Route::get('/register', [MobileAuthController::class, 'showRegistrationForm'])->name('register')->middleware('guest');
Route::post('/register', [MobileAuthController::class, 'register'])->middleware('guest');
Route::post('/logout', [MobileAuthController::class, 'logout'])->name('logout');
Route::get('/cashfree/success', [App\Http\Controllers\CashfreeController::class, 'success'])->name('cashfree.success');

// Staff Login Routes
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/login', [App\Http\Controllers\Auth\StaffAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\StaffAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [App\Http\Controllers\Auth\StaffAuthController::class, 'logout'])->name('logout');
});

// Staff Routes
Route::middleware(['staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\StaffController::class, 'dashboard'])->name('dashboard');
    Route::get('/tasks', [App\Http\Controllers\StaffController::class, 'tasks'])->name('tasks');
    Route::post('/tasks/{taskId}/start', [App\Http\Controllers\StaffController::class, 'startTask'])->name('tasks.start');
    Route::post('/tasks/{taskId}/complete', [App\Http\Controllers\StaffController::class, 'completeTask'])->name('tasks.complete');
    Route::post('/tasks/{taskId}/cancel', [App\Http\Controllers\StaffController::class, 'cancelTask'])->name('tasks.cancel');
    Route::get('/notifications', [App\Http\Controllers\StaffController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{notificationId}/read', [App\Http\Controllers\StaffController::class, 'markNotificationAsRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\StaffController::class, 'markAllNotificationsAsRead'])->name('notifications.mark-all-read');
    Route::get('/checklists', [App\Http\Controllers\StaffController::class, 'checklists'])->name('checklists');
    Route::post('/checklists/{checklistId}/start', [App\Http\Controllers\StaffController::class, 'startChecklist'])->name('checklists.start');
    Route::get('/checklists/{executionUuid}/execute', [App\Http\Controllers\StaffController::class, 'executeChecklist'])->name('checklist.execute');
    Route::post('/checklists/{executionId}/update-item', [App\Http\Controllers\StaffController::class, 'updateChecklistItem'])->name('checklists.update-item');
    Route::post('/checklists/{executionId}/complete', [App\Http\Controllers\StaffController::class, 'completeChecklist'])->name('checklists.complete');
    Route::get('/activity', [App\Http\Controllers\StaffController::class, 'activity'])->name('activity');
    Route::get('/notifications/count', [App\Http\Controllers\StaffController::class, 'getUnreadNotificationsCount'])->name('notifications.count');
    Route::get('/dashboard-data', [App\Http\Controllers\StaffController::class, 'dashboard'])->name('dashboard-data');
});

// Owner Staff Management Routes
Route::middleware(['auth'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/staff', [App\Http\Controllers\OwnerStaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [App\Http\Controllers\OwnerStaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [App\Http\Controllers\OwnerStaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{staffAssignmentId}', [App\Http\Controllers\OwnerStaffController::class, 'show'])->name('staff.show');
    Route::get('/staff/{staffAssignmentId}/edit', [App\Http\Controllers\OwnerStaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staffAssignmentId}', [App\Http\Controllers\OwnerStaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staffAssignmentId}', [App\Http\Controllers\OwnerStaffController::class, 'destroy'])->name('staff.destroy');
    Route::post('/staff/{staffAssignmentId}/assign-task', [App\Http\Controllers\OwnerStaffController::class, 'assignTask'])->name('staff.assign-task');
    Route::post('/staff/{staffAssignmentId}/send-notification', [App\Http\Controllers\OwnerStaffController::class, 'sendNotification'])->name('staff.send-notification');
    Route::post('/staff/{staffAssignmentId}/update-permissions', [App\Http\Controllers\OwnerStaffController::class, 'updatePermissions'])->name('staff.update-permissions');
    Route::get('/staff/stats', [App\Http\Controllers\OwnerStaffController::class, 'getStaffStats'])->name('staff.stats');
    Route::get('/staff/analytics', [App\Http\Controllers\OwnerStaffController::class, 'analytics'])->name('staff.analytics');
});

// API Routes for Staff Management
Route::middleware(['auth'])->prefix('api')->group(function () {
    Route::get('/properties/{propertyId}/roles', function ($propertyId) {
        $user = Auth::user();
        $property = \App\Models\Property::where('id', $propertyId)
                                      ->where('owner_id', $user->id)
                                      ->firstOrFail();
        
        $roles = \App\Models\Role::where('property_id', $propertyId)->get();
        
        return response()->json(['roles' => $roles]);
    });
});

// Protected Routes
Route::middleware(['auth', 'subscription.limits'])->group(function () {
    // Test route for modal scroll lock
    Route::get('/test-modal-scroll', function () {
        return view('test-modal-scroll');
    })->name('test.modal.scroll');
    
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        // If user is staff, redirect to staff dashboard
        if ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        }
        
        $properties = $user->properties()->with(['category', 'location'])->latest()->get();
        
        // If user has no properties, redirect to onboarding wizard
        if ($properties->isEmpty()) {
            return redirect()->route('onboarding.wizard');
        }
        
          // Get dynamic dashboard data
        $dashboardData = [
            'properties' => $properties->load(['propertyAccommodations.reservations', 'category', 'location.city.district.state']),
            'nextBooking' => \App\Models\Reservation::whereHas('accommodation.property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->where('status', 'confirmed')
            ->where('check_in_date', '>=', now())
            ->orderBy('check_in_date')
            ->first(),
            'upcomingBookingsThisWeek' => \App\Models\Reservation::whereHas('accommodation.property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->where('status', 'confirmed')
            ->whereBetween('check_in_date', [now(), now()->addWeek()])
            ->count(),
            'upcomingBookingsThisMonth' => \App\Models\Reservation::whereHas('accommodation.property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->where('status', 'confirmed')
            ->whereBetween('check_in_date', [now(), now()->addMonth()])
            ->count(),
            'topB2bPartner' => \App\Models\B2bPartner::whereHas('reservations.accommodation.property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->withCount('reservations')
            ->orderBy('reservations_count', 'desc')
            ->first(),
            'recentBookings' => \App\Models\Reservation::whereHas('accommodation.property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->with(['guest', 'accommodation.property'])
            ->latest()
            ->limit(5)
            ->get(),
            'pendingBookings' => \App\Models\Reservation::whereHas('accommodation.property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->where('status', 'pending')
            ->with(['guest', 'accommodation.property'])
            ->latest()
            ->get(),
            'activeBookings' => \App\Models\Reservation::whereHas('accommodation.property', function($q) use ($user) {
                $q->where('owner_id', $user->id);
            })->whereIn('status', ['confirmed', 'checked_in'])
            ->with(['guest', 'accommodation.property'])
            ->latest()
            ->get(),
        ];
        
        // Include motivational quotes from partial
        $dashboardData['motivationalQuotes'] = include resource_path('views/partials/dashboard/motivational-quotes.blade.php');
        
        return view('dashboard', $dashboardData);
    })->name('dashboard');
    
    Route::get('/properties/create', [App\Http\Controllers\PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [App\Http\Controllers\PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties', [App\Http\Controllers\PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/{property}/edit', [App\Http\Controllers\PropertyController::class, 'edit'])->name('properties.edit');
    Route::get('/properties/{property}/edit-section', [App\Http\Controllers\PropertyController::class, 'editSection'])->name('properties.edit-section');
    Route::patch('/properties/{property}/update-section', [App\Http\Controllers\PropertyController::class, 'updateSection'])->name('properties.update-section');
    Route::post('/properties/{property}/test-ajax', [App\Http\Controllers\PropertyController::class, 'testAjax'])->name('properties.test-ajax');
    
    // Accommodation Routes
    Route::get('/properties/{property}/accommodations/create', [App\Http\Controllers\PropertyController::class, 'createAccommodation'])->name('properties.accommodations.create');
    Route::get('/properties/{property}/accommodations/{accommodation}/edit', [App\Http\Controllers\PropertyController::class, 'editAccommodation'])->name('properties.accommodations.edit');
    Route::post('/properties/{property}/accommodations/store', [App\Http\Controllers\PropertyController::class, 'storeAccommodation'])->name('properties.accommodations.store');
    Route::post('/properties/{property}/accommodations/{accommodation}/update', [App\Http\Controllers\PropertyController::class, 'updateAccommodation'])->name('properties.accommodations.update');
    Route::delete('/properties/{property}/accommodations/{accommodation}/delete', [App\Http\Controllers\PropertyController::class, 'deleteAccommodation'])->name('properties.accommodations.delete');
    
    // Dedicated Accommodation Routes
    Route::get('/accommodations', [App\Http\Controllers\AccommodationController::class, 'index'])->name('accommodations.index');
    Route::get('/accommodations/create', [App\Http\Controllers\AccommodationController::class, 'create'])->name('accommodations.create');
    Route::post('/accommodations', [App\Http\Controllers\AccommodationController::class, 'store'])->name('accommodations.store');
    Route::get('/accommodations/{accommodation}', [App\Http\Controllers\AccommodationController::class, 'show'])->name('accommodations.show');
    Route::get('/accommodations/{accommodation}/edit', [App\Http\Controllers\AccommodationController::class, 'edit'])->name('accommodations.edit');
    Route::put('/accommodations/{accommodation}', [App\Http\Controllers\AccommodationController::class, 'update'])->name('accommodations.update');
    Route::delete('/accommodations/{accommodation}', [App\Http\Controllers\AccommodationController::class, 'destroy'])->name('accommodations.destroy');
    
    // Photo Routes
    Route::post('/properties/{property}/photos', [App\Http\Controllers\PropertyController::class, 'storePhotos'])->name('properties.photos.store');
    Route::delete('/properties/{property}/photos/{photo}', [App\Http\Controllers\PropertyController::class, 'deletePhoto'])->name('properties.photos.delete');
    
    // Booking Management Routes
    Route::get('/bookings', function () {
        return view('bookings.index');
    })->name('bookings.index');
    Route::get('/bookings/calendar', function () {
        return view('bookings.calendar');
    })->name('bookings.calendar');
    Route::get('/bookings/create', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [App\Http\Controllers\BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/edit', [App\Http\Controllers\BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [App\Http\Controllers\BookingController::class, 'update'])->name('bookings.update');
    Route::get('/bookings-cancelled', [App\Http\Controllers\BookingController::class, 'cancelled'])->name('bookings.cancelled');
    
    // Invoice Routes
    Route::get('/bookings/{booking}/invoice/download', [App\Http\Controllers\InvoiceController::class, 'download'])->name('bookings.invoice.download');
    Route::get('/bookings/bulk-invoice/download', [App\Http\Controllers\InvoiceController::class, 'bulkDownload'])->name('bookings.bulk-invoice.download');
    
    // Check-in/Check-out Routes
    Route::get('/checkin', [App\Http\Controllers\CheckInController::class, 'index'])->name('checkin.index');
    Route::get('/checkin/confirmed-bookings', [App\Http\Controllers\CheckInController::class, 'confirmedBookings'])->name('checkin.confirmed-bookings');
    Route::post('/checkin/{reservationUuid}/update-customer', [App\Http\Controllers\CheckInController::class, 'updateCustomerDetails'])->name('checkin.update-customer');
    Route::get('/checkin/{reservationUuid}', [App\Http\Controllers\CheckInController::class, 'show'])->name('checkin.show');
    Route::post('/checkin/{reservationUuid}', [App\Http\Controllers\CheckInController::class, 'store'])->name('checkin.store');
    Route::get('/checkin/{checkInUuid}/success', [App\Http\Controllers\CheckInController::class, 'success'])->name('checkin.success');
    Route::get('/checkin/{checkInUuid}/details', [App\Http\Controllers\CheckInController::class, 'details'])->name('checkin.details');
    Route::get('/api/checkin/{reservationUuid}/booking-details', [App\Http\Controllers\CheckInController::class, 'getBookingDetails'])->name('api.checkin.booking-details');
    
    Route::get('/checkout', [App\Http\Controllers\CheckOutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/{reservationUuid}', [App\Http\Controllers\CheckOutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{reservationUuid}', [App\Http\Controllers\CheckOutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/{checkOutUuid}/success', [App\Http\Controllers\CheckOutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/{checkOutUuid}/details', [App\Http\Controllers\CheckOutController::class, 'details'])->name('checkout.details');
    Route::post('/checkout/{checkOutUuid}/mark-clean', [App\Http\Controllers\CheckOutController::class, 'markRoomClean'])->name('checkout.mark-clean');
    Route::get('/api/checkout/{reservationUuid}/booking-details', [App\Http\Controllers\CheckOutController::class, 'getBookingDetails'])->name('api.checkout.booking-details');
    
    // API Routes for Alpine.js
    Route::prefix('api')->group(function () {
        Route::get('/properties', [App\Http\Controllers\BookingController::class, 'getProperties']);
        Route::get('/properties/accommodation-count', [App\Http\Controllers\BookingController::class, 'getAccommodationCount']);
        Route::get('/properties/{propertyId}', [App\Http\Controllers\BookingController::class, 'getProperty']);
        Route::post('/properties', [App\Http\Controllers\PropertyController::class, 'store']);
        Route::get('/properties/{propertyId}/accommodations', [App\Http\Controllers\BookingController::class, 'getAccommodations']);
        Route::get('/properties/{property}/accommodations', [App\Http\Controllers\PropertyController::class, 'getAccommodations']);
        Route::post('/properties/{property}/accommodations', [App\Http\Controllers\PropertyController::class, 'storeAccommodation']);
        Route::post('/accommodations/available', [App\Http\Controllers\BookingController::class, 'findAvailableAccommodations']);
        Route::get('/guests', [App\Http\Controllers\BookingController::class, 'getGuests']);
        Route::get('/partners', [App\Http\Controllers\BookingController::class, 'getPartners']);
        Route::get('/partners/{partnerId}/reserved-customer', [App\Http\Controllers\BookingController::class, 'getPartnerReservedCustomer']);
        Route::get('/accommodations/{accommodationId}/reserved-customer', [App\Http\Controllers\BookingController::class, 'getAccommodationReservedCustomer']);
        Route::get('/bookings', [App\Http\Controllers\BookingController::class, 'index']);

        Route::patch('/bookings/{booking}/toggle-status', [App\Http\Controllers\BookingController::class, 'toggleStatus']);
        Route::patch('/bookings/{booking}/confirm', [App\Http\Controllers\BookingController::class, 'confirm']);
        Route::patch('/bookings/{booking}/cancel', [App\Http\Controllers\BookingController::class, 'cancel']);
        Route::patch('/bookings/{booking}/reactivate', [App\Http\Controllers\BookingController::class, 'reactivate']);
        
        // Property Delete Request Routes
        Route::post('/property-delete-requests', [App\Http\Controllers\PropertyDeleteRequestController::class, 'store']);
        Route::get('/property-delete-requests', [App\Http\Controllers\PropertyDeleteRequestController::class, 'index']);
        Route::get('/property-delete-requests/{deleteRequest}', [App\Http\Controllers\PropertyDeleteRequestController::class, 'show']);
        Route::delete('/property-delete-requests/{deleteRequest}', [App\Http\Controllers\PropertyDeleteRequestController::class, 'cancel']);
    });
    
    // Customer Management Routes
    Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [App\Http\Controllers\CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [App\Http\Controllers\CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
    
    // B2B Management Routes
    Route::get('/b2b', [App\Http\Controllers\B2bController::class, 'index'])->name('b2b.index')->middleware('subscription:b2b');
    Route::get('/b2b/create', [App\Http\Controllers\B2bController::class, 'create'])->name('b2b.create')->middleware('subscription:b2b');
    Route::post('/b2b', [App\Http\Controllers\B2bController::class, 'store'])->name('b2b.store')->middleware('subscription:b2b');
    Route::get('/b2b/{b2b}', [App\Http\Controllers\B2bController::class, 'show'])->name('b2b.show')->middleware('subscription:b2b');
    Route::get('/b2b/{b2b}/edit', [App\Http\Controllers\B2bController::class, 'edit'])->name('b2b.edit')->middleware('subscription:b2b');
    Route::put('/b2b/{b2b}', [App\Http\Controllers\B2bController::class, 'update'])->name('b2b.update')->middleware('subscription:b2b');
    Route::delete('/b2b/{b2b}', [App\Http\Controllers\B2bController::class, 'destroy'])->name('b2b.destroy')->middleware('subscription:b2b');
    Route::patch('/b2b/{b2b}/toggle-status', [App\Http\Controllers\B2bController::class, 'toggleStatus'])->name('b2b.toggle-status')->middleware('subscription:b2b');
    

    
    // Pricing Management Routes
    Route::get('/pricing', function () {
        return view('pricing.index');
    })->name('pricing.index');
    Route::get('/pricing/calendar', function () {
        return view('pricing.calendar');
    })->name('pricing.calendar');
    
    
    // Onboarding Routes
    Route::get('/onboarding', function () {
        $propertyCategories = \App\Models\PropertyCategory::all();
        return view('onboarding.wizard', compact('propertyCategories'));
    })->name('onboarding.wizard');
    
    // Booking Dashboard Routes
    Route::get('/booking-dashboard', function () {
        $user = auth()->user();
        $recentBookings = $user->reservations()
            ->with(['propertyAccommodation.property', 'guest'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'guest_name' => $booking->guest->name,
                    'property_name' => $booking->propertyAccommodation->property->name,
                    'accommodation_name' => $booking->propertyAccommodation->name,
                    'check_in_date' => $booking->check_in_date->format('M d'),
                    'check_out_date' => $booking->check_out_date->format('M d'),
                    'total_amount' => $booking->total_amount
                ];
            });
        
        $stats = [
            'total_bookings' => $user->reservations()->count(),
            'confirmed_bookings' => $user->reservations()->where('status', 'confirmed')->count(),
            'pending_bookings' => $user->reservations()->where('status', 'pending')->count(),
            'total_revenue' => $user->reservations()->sum('total_amount')
        ];
        
        return view('booking-dashboard', compact('recentBookings', 'stats'));
    })->name('booking.dashboard');
    
    // Enhanced Booking Routes
    Route::get('/bookings/enhanced-create', function () {
        $user = auth()->user();
        $properties = $user->properties()->with(['category', 'propertyAccommodations'])->get();
        $b2bPartners = $user->b2bPartners()->where('status', 'active')->get();
        
        return view('bookings.enhanced-create', compact('properties', 'b2bPartners'));
    })->name('bookings.enhanced-create');
    Route::get('/subscription/plans', [App\Http\Controllers\SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::get('/subscription/limit-exceeded', function () {
        $user = auth()->user();
        $usage = $user->getUsagePercentage();
        $subscriptionStatus = $user->subscription_status ?? 'trial';
        $subscriptionEndsAt = $user->subscription_ends_at;
        $activeSubscription = $user->activeSubscription;
        
        // Get real plan details based on subscription status
        $planDetails = [
            'name' => ucfirst($subscriptionStatus),
            'accommodations_allowed' => $usage['accommodations']['max'] ?? 0,
            'accommodations_used' => $usage['accommodations']['used'] ?? 0,
            'accommodations_exceeded' => ($usage['accommodations']['used'] ?? 0) - ($usage['accommodations']['max'] ?? 0),
            'subscription_ends_at' => $subscriptionEndsAt,
            'is_trial' => $subscriptionStatus === 'trial',
            'billing_cycle' => $user->billing_cycle,
            'properties_limit' => $usage['properties']['max'] ?? 0,
            'properties_used' => $usage['properties']['used'] ?? 0,
            'active_subscription' => $activeSubscription,
            'addon_count' => $activeSubscription ? $activeSubscription->addons()->where('cycle_end', '>', now())->sum('qty') : 0,
            'base_accommodation_limit' => $activeSubscription ? $activeSubscription->base_accommodation_limit : $usage['accommodations']['max'],
            'addon_price_per_month' => 99 // Real pricing from the app
        ];
        
        return view('subscription.limit-exceeded', compact('planDetails'));
    })->name('subscription.limit-exceeded');
    Route::get('/welcome-trial', function () {
        return view('welcome-trial');
    })->name('welcome.trial');
    
    // Cashfree Payment Routes
    Route::post('/cashfree/create-order', [App\Http\Controllers\CashfreeController::class, 'createOrder'])->name('cashfree.create-order');
    
    
    
    // Test payment success endpoint
    Route::get('/test-payment-success', function () {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Simulate successful payment
        $user->update([
            'subscription_status' => 'professional',
            'subscription_ends_at' => now()->addMonth(),
            'is_trial_active' => false,
            'properties_limit' => 5,
            'billing_cycle' => 'monthly',
        ]);
        
        return redirect()->route('subscription.plans', ['payment' => 'success'])
            ->with('success', 'Test payment successful! Your subscription has been activated.');
    })->name('test.payment.success');
    
    // Admin sample download route
    Route::get('/admin/download-sample-locations', function () {
        $sampleData = [
            'countries' => [
                [
                    'name' => 'India',
                    'code' => 'IN',
                    'states' => [
                        [
                            'name' => 'Maharashtra',
                            'code' => 'MH',
                            'districts' => [
                                [
                                    'name' => 'Mumbai',
                                    'cities' => [
                                        [
                                            'name' => 'Mumbai',
                                            'pincodes' => [
                                                ['code' => '400001'],
                                                ['code' => '400002'],
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        [
                            'name' => 'Karnataka',
                            'code' => 'KA',
                            'districts' => [
                                [
                                    'name' => 'Bangalore Urban',
                                    'cities' => [
                                        [
                                            'name' => 'Bangalore',
                                            'pincodes' => [
                                                ['code' => '560001'],
                                                ['code' => '560002'],
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return response()->json($sampleData, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="sample-locations.json"'
        ]);
    })->name('admin.download-sample-locations');
});
