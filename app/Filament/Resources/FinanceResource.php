<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinanceResource\Pages;
use App\Models\User;
use App\Models\Referral;
use App\Models\ReferralWithdrawal;
use App\Models\Subscription;
use App\Models\SubscriptionAddon;
use App\Models\Payment;
use App\Models\Refund;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class FinanceResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Finance Dashboard';
    protected static ?string $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        // Return a dummy query that will be overridden by the table columns
        return parent::getEloquentQuery()->where('id', 0);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Revenue Overview')
                    ->schema([
                        Forms\Components\Placeholder::make('total_revenue')
                            ->label('Total Revenue')
                            ->content(function () {
                                $totalRevenue = Payment::where('status', 'completed')->sum('amount');
                                return '₹' . number_format($totalRevenue);
                            }),
                        Forms\Components\Placeholder::make('subscription_revenue')
                            ->label('Subscription Revenue')
                            ->content(function () {
                                $subscriptionRevenue = Subscription::where('status', 'active')->sum('price');
                                return '₹' . number_format($subscriptionRevenue);
                            }),
                        Forms\Components\Placeholder::make('addon_revenue')
                            ->label('Add-on Revenue')
                            ->content(function () {
                                $addonRevenue = SubscriptionAddon::where('cycle_end', '>', now())->get()->sum(function($addon) {
                                    return $addon->qty * $addon->unit_price;
                                });
                                return '₹' . number_format($addonRevenue);
                            }),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('total_subscription_revenue')
                    ->label('Total Subscription Revenue')
                    ->getStateUsing(function () {
                        $activeSubscriptions = Subscription::where('status', 'active')->get();
                        $totalRevenue = $activeSubscriptions->sum('total_subscription_amount');
                        return '₹' . number_format($totalRevenue);
                    })
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('base_plan_revenue')
                    ->label('Base Plan Revenue')
                    ->getStateUsing(function () {
                        $baseRevenue = Subscription::where('status', 'active')->sum('price');
                        return '₹' . number_format($baseRevenue);
                    })
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('addon_revenue')
                    ->label('Add-on Revenue')
                    ->getStateUsing(function () {
                        $addonRevenue = SubscriptionAddon::where('cycle_end', '>', now())->get()->sum(function($addon) {
                            return $addon->qty * $addon->unit_price;
                        });
                        return '₹' . number_format($addonRevenue);
                    })
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('active_subscriptions')
                    ->label('Active Subscriptions')
                    ->getStateUsing(function () {
                        return Subscription::where('status', 'active')->count();
                    })
                    ->badge()
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('active_addons')
                    ->label('Active Add-ons')
                    ->getStateUsing(function () {
                        return SubscriptionAddon::where('cycle_end', '>', now())->sum('qty');
                    })
                    ->badge()
                    ->color('secondary'),
                
                Tables\Columns\TextColumn::make('total_payments')
                    ->label('Total Payments')
                    ->getStateUsing(function () {
                        $totalPayments = Payment::where('status', 'completed')->sum('amount');
                        return '₹' . number_format($totalPayments);
                    })
                    ->badge()
                    ->color('success'),
                
                Tables\Columns\TextColumn::make('pending_payments')
                    ->label('Pending Payments')
                    ->getStateUsing(function () {
                        $pendingPayments = Payment::where('status', 'pending')->sum('amount');
                        return '₹' . number_format($pendingPayments);
                    })
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('total_refunds')
                    ->label('Total Refunds')
                    ->getStateUsing(function () {
                        $totalRefunds = Refund::where('status', 'completed')->get()->sum('amount_in_rupees');
                        return '₹' . number_format($totalRefunds);
                    })
                    ->badge()
                    ->color('danger'),
                
                Tables\Columns\TextColumn::make('referral_payouts')
                    ->label('Referral Payouts')
                    ->getStateUsing(function () {
                        return '₹' . number_format(Referral::where('status', 'completed')->sum('reward_amount'));
                    })
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('pending_withdrawals')
                    ->label('Pending Withdrawals')
                    ->getStateUsing(function () {
                        return '₹' . number_format(ReferralWithdrawal::where('status', 'pending')->sum('amount'));
                    })
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('net_revenue')
                    ->label('Net Revenue')
                    ->getStateUsing(function () {
                        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
                        $totalRefunds = Refund::where('status', 'completed')->get()->sum('amount_in_rupees');
                        $referralPayouts = Referral::where('status', 'completed')->sum('reward_amount');
                        $netRevenue = $totalRevenue - $totalRefunds - $referralPayouts;
                        return '₹' . number_format($netRevenue);
                    })
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinances::route('/'),
        ];
    }
}