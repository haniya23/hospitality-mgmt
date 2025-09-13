<?php

namespace App\Livewire;

use Livewire\Component;

class ResourceManagement extends Component
{
    public function render()
    {
        $resources = [
            [
                'title' => 'Property Management',
                'description' => 'Manage your properties, accommodations, and amenities',
                'icon' => 'building',
                'route' => 'properties.index',
                'color' => 'purple'
            ],
            [
                'title' => 'Pricing & Calendar',
                'description' => 'Set rates, manage availability, and pricing rules',
                'icon' => 'calendar',
                'route' => 'pricing.calendar',
                'color' => 'green'
            ],
            [
                'title' => 'Reports & Analytics',
                'description' => 'View booking reports, revenue analytics, and insights',
                'icon' => 'chart',
                'route' => 'reports.analytics',
                'color' => 'blue'
            ],
            [
                'title' => 'Guest Management',
                'description' => 'Manage guest profiles, preferences, and history',
                'icon' => 'users',
                'route' => 'customers.index',
                'color' => 'teal'
            ],
            [
                'title' => 'B2B Partners',
                'description' => 'Manage business partnerships and commissions',
                'icon' => 'handshake',
                'route' => 'b2b.dashboard',
                'color' => 'indigo'
            ],
            [
                'title' => 'Booking Management',
                'description' => 'Handle reservations, check-ins, and cancellations',
                'icon' => 'clipboard',
                'route' => 'bookings.index',
                'color' => 'emerald'
            ]
        ];

        return view('livewire.resource-management', compact('resources'))
            ->extends('layouts.mobile')
            ->section('content');
    }
}