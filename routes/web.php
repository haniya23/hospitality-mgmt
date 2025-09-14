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
    Route::post('/bookings', [App\Http\Controllers\BookingController::class, 'store'])->name('bookings.store');
    
    // Customer Management Routes
    Route::get('/customers', App\Livewire\CustomerManagement::class)->name('customers.index');
    
    // B2B Management Routes
    Route::get('/b2b', App\Livewire\B2BManagement::class)->name('b2b.dashboard');
    
    // Resources Management Routes
    Route::get('/resources', App\Livewire\ResourceManagement::class)->name('resources.index');
    
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
    })->name('reports.analytics');
    
    // Admin Routes
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::patch('/admin/properties/{property}/approve', [App\Http\Controllers\AdminController::class, 'approveProperty'])->name('admin.properties.approve');
    Route::patch('/admin/properties/{property}/reject', [App\Http\Controllers\AdminController::class, 'rejectProperty'])->name('admin.properties.reject');
});
