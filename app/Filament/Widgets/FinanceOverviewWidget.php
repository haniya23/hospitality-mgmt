<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\SubscriptionAddon;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Referral;
use App\Models\ReferralWithdrawal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Calculate subscription revenue
        $activeSubscriptions = Subscription::where('status', 'active')->get();
        $totalSubscriptionRevenue = $activeSubscriptions->sum('total_subscription_amount');
        $basePlanRevenue = $activeSubscriptions->sum('price');
        $addonRevenue = $activeSubscriptions->sum('total_addon_amount');

        // Calculate payment metrics
        $totalPayments = Payment::where('status', 'completed')->sum('amount');
        $pendingPayments = Payment::where('status', 'pending')->sum('amount');
        $totalRefunds = Refund::where('status', 'completed')->get()->sum('amount_in_rupees');

        // Calculate referral metrics
        $referralPayouts = Referral::where('status', 'completed')->sum('reward_amount');
        $pendingWithdrawals = ReferralWithdrawal::where('status', 'pending')->sum('amount');

        // Calculate net revenue
        $netRevenue = $totalPayments - $totalRefunds - $referralPayouts;

        // Calculate subscription counts
        $activeSubscriptionCount = Subscription::where('status', 'active')->count();
        $activeAddonCount = SubscriptionAddon::where('cycle_end', '>', now())->sum('qty');

        return [
            Stat::make('Total Revenue', '₹' . number_format($totalPayments))
                ->description('All completed payments')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Subscription Revenue', '₹' . number_format($totalSubscriptionRevenue))
                ->description('Base plans + Add-ons')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('primary'),

            Stat::make('Add-on Revenue', '₹' . number_format($addonRevenue))
                ->description($activeAddonCount . ' active add-ons')
                ->descriptionIcon('heroicon-m-plus-circle')
                ->color('warning'),

            Stat::make('Net Revenue', '₹' . number_format($netRevenue))
                ->description('After refunds & payouts')
                ->descriptionIcon('heroicon-m-calculator')
                ->color($netRevenue >= 0 ? 'success' : 'danger'),

            Stat::make('Active Subscriptions', $activeSubscriptionCount)
                ->description('Currently active')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Pending Payments', '₹' . number_format($pendingPayments))
                ->description('Awaiting completion')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Refunds', '₹' . number_format($totalRefunds))
                ->description('Completed refunds')
                ->descriptionIcon('heroicon-m-arrow-uturn-left')
                ->color('danger'),

            Stat::make('Referral Payouts', '₹' . number_format($referralPayouts))
                ->description('Completed payouts')
                ->descriptionIcon('heroicon-m-gift')
                ->color('info'),

            Stat::make('Pending Withdrawals', '₹' . number_format($pendingWithdrawals))
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}
