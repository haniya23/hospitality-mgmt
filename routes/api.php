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
});
