<?php

use App\Http\Controllers\Auth\MobileAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [MobileAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [MobileAuthController::class, 'login']);
Route::get('/register', [MobileAuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [MobileAuthController::class, 'register']);
Route::post('/logout', [MobileAuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        $properties = auth()->user()->properties()->latest()->get();
        return view('dashboard', compact('properties'));
    })->name('dashboard');
    
    Route::get('/properties/create', [App\Http\Controllers\PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [App\Http\Controllers\PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties', [App\Http\Controllers\PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/{property}/edit', [App\Http\Controllers\PropertyController::class, 'edit'])->name('properties.edit');
    Route::patch('/properties/{property}/update-section', [App\Http\Controllers\PropertyController::class, 'updateSection'])->name('properties.update-section');
    
    // Booking Management Routes
    Route::get('/bookings', function () {
        return view('bookings.index');
    })->name('bookings.index');
    Route::get('/bookings/create', [App\Http\Controllers\BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}/edit', [App\Http\Controllers\BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [App\Http\Controllers\BookingController::class, 'update'])->name('bookings.update');
    
    // API Routes for Alpine.js
    Route::prefix('api')->group(function () {
        Route::get('/properties', [App\Http\Controllers\BookingController::class, 'getProperties']);
        Route::get('/properties/{propertyId}/accommodations', [App\Http\Controllers\BookingController::class, 'getAccommodations']);
        Route::get('/guests', [App\Http\Controllers\BookingController::class, 'getGuests']);
        Route::get('/partners', [App\Http\Controllers\BookingController::class, 'getPartners']);
        Route::get('/bookings', [App\Http\Controllers\BookingController::class, 'index']);

        Route::patch('/bookings/{booking}/toggle-status', [App\Http\Controllers\BookingController::class, 'toggleStatus']);
        Route::patch('/bookings/{booking}/cancel', [App\Http\Controllers\BookingController::class, 'cancel']);
    });
    
    // Customer Management Routes
    Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [App\Http\Controllers\CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [App\Http\Controllers\CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
    
    // B2B Management Routes
    Route::get('/b2b', function () {
        return view('b2b.index');
    })->name('b2b.dashboard')->middleware('subscription:b2b');
    

    
    // Pricing Management Routes
    Route::get('/pricing', function () {
        return view('pricing.index');
    })->name('pricing.index');
    Route::get('/pricing/calendar', function () {
        return view('pricing.calendar');
    })->name('pricing.calendar');
    
    // Reports & Analytics Routes
    Route::get('/reports', function () {
        return view('reports.analytics');
    })->name('reports.analytics')->middleware('subscription:advanced_reports');
    
    // Subscription Routes
    Route::get('/subscription/plans', [App\Http\Controllers\SubscriptionController::class, 'plans'])->name('subscription.plans');
    Route::post('/subscription/subscribe', [App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::get('/welcome-trial', function () {
        return view('welcome-trial');
    })->name('welcome.trial');
    
    // Admin Routes
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::patch('/admin/properties/{property}/approve', [App\Http\Controllers\AdminController::class, 'approveProperty'])->name('admin.properties.approve');
    Route::patch('/admin/properties/{property}/reject', [App\Http\Controllers\AdminController::class, 'rejectProperty'])->name('admin.properties.reject');
    Route::get('/admin/subscriptions', [App\Http\Controllers\AdminController::class, 'subscriptions'])->name('admin.subscriptions');
    Route::patch('/admin/subscriptions/{request}/approve', [App\Http\Controllers\AdminController::class, 'approveSubscription'])->name('admin.subscriptions.approve');
});
