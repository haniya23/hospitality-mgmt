<?php

namespace App\Providers;

use App\Models\PropertyAccommodation;
use App\Models\StaffMember;
use App\Models\Task;
use App\Policies\PropertyAccommodationPolicy;
use App\Policies\StaffMemberPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        PropertyAccommodation::class => PropertyAccommodationPolicy::class,
        StaffMember::class => StaffMemberPolicy::class,
        Task::class => TaskPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}