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
    


    
    // Admin Routes
    Route::get('/admin', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::patch('/admin/properties/{property}/approve', [App\Http\Controllers\AdminController::class, 'approveProperty'])->name('admin.properties.approve');
    Route::patch('/admin/properties/{property}/reject', [App\Http\Controllers\AdminController::class, 'rejectProperty'])->name('admin.properties.reject');
});
