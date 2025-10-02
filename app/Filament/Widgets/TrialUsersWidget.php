<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TrialUsersWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $trialUsers = User::where('subscription_status', 'trial')
                          ->where('is_trial_active', true)
                          ->get();
        
        $activeTrials = $trialUsers->where('trial_ends_at', '>', now())->count();
        $expiringTrials = $trialUsers->where('trial_ends_at', '<=', now()->addDays(7))
                                    ->where('trial_ends_at', '>', now())
                                    ->count();
        $expiredTrials = $trialUsers->where('trial_ends_at', '<', now())->count();

        return [
            Stat::make('Active Trial Users', $activeTrials)
                ->description('Currently on trial')
                ->descriptionIcon('heroicon-m-gift')
                ->color('success'),

            Stat::make('Expiring Soon', $expiringTrials)
                ->description('Expire within 7 days')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Expired Trials', $expiredTrials)
                ->description('Past expiration date')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
