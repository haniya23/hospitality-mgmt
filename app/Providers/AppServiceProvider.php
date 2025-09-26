<?php

namespace App\Providers;

use App\Models\B2bPartner;
use App\Observers\B2bPartnerObserver;
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
    }
}
