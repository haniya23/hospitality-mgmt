<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\User;
use App\Models\Guest;
use App\Models\B2bPartner;
use App\Models\SubscriptionRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Properties', Property::where('status', 'pending')->count())
                ->description('Properties awaiting approval')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            
            Stat::make('Total Users', User::where('is_admin', false)->count())
                ->description('Registered property owners')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->chart([1, 3, 5, 10, 20, 40]),
            
            Stat::make('Total Properties', Property::count())
                ->description('All properties in system')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary')
                ->chart([2, 4, 6, 8, 12, 16, 20]),
            
            Stat::make('Pending Subscriptions', SubscriptionRequest::where('status', 'pending')->count())
                ->description('Subscription requests')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('warning')
                ->chart([1, 2, 1, 3, 2, 4, 3]),
            
            Stat::make('Total Customers', Guest::count())
                ->description('Guest customers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info')
                ->chart([5, 10, 15, 25, 30, 40, 50]),
            
            Stat::make('B2B Partners', B2bPartner::count())
                ->description('Business partners')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('gray')
                ->chart([1, 1, 2, 3, 3, 4, 5]),
        ];
    }
}