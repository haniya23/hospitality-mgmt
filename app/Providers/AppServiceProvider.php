<?php

namespace App\Providers;

use App\Models\B2bPartner;
use App\Models\Payment;
use App\Models\PropertyAccommodation;
use App\Models\Reservation;
use App\Observers\B2bPartnerObserver;
use App\Observers\PaymentObserver;
use App\Observers\PropertyAccommodationObserver;
use App\Observers\ReservationObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register model observers
        B2bPartner::observe(B2bPartnerObserver::class);
        Payment::observe(PaymentObserver::class);
        PropertyAccommodation::observe(PropertyAccommodationObserver::class);
        Reservation::observe(ReservationObserver::class);
    }
}
