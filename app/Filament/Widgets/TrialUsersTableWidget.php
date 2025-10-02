<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Support\Enums\FontWeight;

class TrialUsersTableWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Trial Users';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('subscription_status', 'trial')
                    ->where('is_trial_active', true)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('mobile_number')
                    ->label('Mobile')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No email'),

                Tables\Columns\TextColumn::make('trial_plan')
                    ->label('Trial Plan')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->description(function (User $record): string {
                        $days = now()->diffInDays($record->trial_ends_at, false);
                        return $days > 0 ? "{$days} days remaining" : "Expired";
                    })
                    ->color(function (User $record): string {
                        $days = now()->diffInDays($record->trial_ends_at, false);
                        return $days <= 7 ? 'danger' : ($days <= 30 ? 'warning' : 'success');
                    }),

                Tables\Columns\IconColumn::make('is_trial_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('properties_count')
                    ->label('Properties')
                    ->counts('properties')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Expiring Soon (7 days)')
                    ->query(function ($query) { 
                        return $query->where('trial_ends_at', '<=', now()->addDays(7))
                                    ->where('trial_ends_at', '>', now());
                    }),
                
                Tables\Filters\Filter::make('expired')
                    ->label('Expired')
                    ->query(function ($query) { 
                        return $query->where('trial_ends_at', '<', now());
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('view_user')
                    ->label('View User')
                    ->icon('heroicon-o-eye')
                    ->url(fn (User $record) => '#') // TODO: Link to user view
                    ->color('gray'),
                
                Tables\Actions\Action::make('upgrade_to_starter')
                    ->label('Upgrade to Starter')
                    ->icon('heroicon-o-arrow-up')
                    ->color('success')
                    ->action(function (User $record) {
                        $record->update([
                            'subscription_status' => 'starter',
                            'is_trial_active' => false,
                            'subscription_ends_at' => now()->addYear(),
                            'properties_limit' => 1,
                        ]);
                    })
                    ->requiresConfirmation(),
                
                Tables\Actions\Action::make('upgrade_to_professional')
                    ->label('Upgrade to Professional')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->action(function (User $record) {
                        $record->update([
                            'subscription_status' => 'professional',
                            'is_trial_active' => false,
                            'subscription_ends_at' => now()->addYear(),
                            'properties_limit' => 5,
                        ]);
                    })
                    ->requiresConfirmation(),
            ])
            ->defaultSort('trial_ends_at', 'asc');
    }
}
