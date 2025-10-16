<?php

namespace App\Providers;

use App\Models\B2bPartner;
use App\Models\PropertyAccommodation;
use App\Models\StaffMember;
use App\Observers\B2bPartnerObserver;
use App\Observers\PropertyAccommodationObserver;
use App\Observers\StaffMemberObserver;
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
        PropertyAccommodation::observe(PropertyAccommodationObserver::class);
        StaffMember::observe(StaffMemberObserver::class);

        // Register custom Blade directives for permissions
        Blade::if('staffCan', function ($permission) {
            $user = auth()->user();
            return $user && $user->staffMember && $user->staffMember->hasPermission($permission);
        });

        Blade::if('isManager', function () {
            $user = auth()->user();
            return $user && $user->staffMember && $user->staffMember->isManager();
        });

        Blade::if('isSupervisor', function () {
            $user = auth()->user();
            return $user && $user->staffMember && $user->staffMember->isSupervisor();
        });

        Blade::if('isStaff', function () {
            $user = auth()->user();
            return $user && $user->staffMember && $user->staffMember->isStaff();
        });
    }
}
