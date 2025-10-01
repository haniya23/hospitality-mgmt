<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SubscriptionStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $trialSubscriptions = Subscription::where('status', 'trial')->count();
        $expiredSubscriptions = Subscription::where('status', 'expired')->count();
        
        $monthlyRevenue = Payment::where('status', 'completed')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('amount_cents') / 100;
            
        $totalRevenue = Payment::where('status', 'completed')
            ->sum('amount_cents') / 100;
            
        $subscriptionsExpiringSoon = Subscription::where('status', 'active')
            ->where('current_period_end', '<=', now()->addDays(7))
            ->count();

        return [
            Stat::make('Total Subscriptions', $totalSubscriptions)
                ->description('All time subscriptions')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary'),
                
            Stat::make('Active Subscriptions', $activeSubscriptions)
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
                
            Stat::make('Trial Subscriptions', $trialSubscriptions)
                ->description('In trial period')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
                
            Stat::make('Monthly Revenue', '₹' . number_format($monthlyRevenue, 2))
                ->description('This month')
                ->descriptionIcon('heroicon-m-currency-rupee')
                ->color('success'),
                
            Stat::make('Total Revenue', '₹' . number_format($totalRevenue, 2))
                ->description('All time')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('primary'),
                
            Stat::make('Expiring Soon', $subscriptionsExpiringSoon)
                ->description('Next 7 days')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($subscriptionsExpiringSoon > 0 ? 'warning' : 'success'),
        ];
    }
}
