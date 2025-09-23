<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinanceResource\Pages;
use App\Models\User;
use App\Models\Referral;
use App\Models\ReferralWithdrawal;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FinanceResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Finance Reports';
    protected static ?string $navigationGroup = 'Finance';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscription_revenue')
                    ->label('Monthly Subscription Revenue')
                    ->getStateUsing(function () {
                        $starter = User::where('subscription_status', 'starter')->count() * 399;
                        $professional = User::where('subscription_status', 'professional')->count() * 699;
                        return '₹' . number_format($starter + $professional);
                    }),
                Tables\Columns\TextColumn::make('referral_payouts')
                    ->label('Total Referral Payouts')
                    ->getStateUsing(function () {
                        return '₹' . number_format(Referral::where('status', 'completed')->sum('reward_amount'));
                    }),
                Tables\Columns\TextColumn::make('pending_withdrawals')
                    ->label('Pending Withdrawals')
                    ->getStateUsing(function () {
                        return '₹' . number_format(ReferralWithdrawal::where('status', 'pending')->sum('amount'));
                    }),
                Tables\Columns\TextColumn::make('net_revenue')
                    ->label('Net Revenue')
                    ->getStateUsing(function () {
                        $subscription = (User::where('subscription_status', 'starter')->count() * 399) + 
                                       (User::where('subscription_status', 'professional')->count() * 699);
                        $referrals = Referral::where('status', 'completed')->sum('reward_amount');
                        return '₹' . number_format($subscription - $referrals);
                    }),
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