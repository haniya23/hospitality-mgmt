<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationGroup = 'Subscriptions';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('plan_slug')
                    ->options([
                        'trial' => 'Trial Plan',
                        'starter' => 'Starter Plan',
                        'professional' => 'Professional Plan',
                    ])
                    ->required()
                    ->reactive(),
                Forms\Components\TextInput::make('plan_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'trial' => 'Trial',
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('base_accommodation_limit')
                    ->required()
                    ->numeric()
                    ->default(3),
                Forms\Components\TextInput::make('addon_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\DateTimePicker::make('start_at')
                    ->required(),
                Forms\Components\DateTimePicker::make('current_period_end')
                    ->required(),
                Forms\Components\Select::make('billing_interval')
                    ->options([
                        'month' => 'Monthly',
                        'year' => 'Yearly',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('price_cents')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->label('Price (in cents)'),
                Forms\Components\TextInput::make('currency')
                    ->required()
                    ->maxLength(3)
                    ->default('INR'),
                Forms\Components\TextInput::make('cashfree_order_id')
                    ->maxLength(255)
                    ->label('Cashfree Order ID'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan_name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Trial Plan' => 'gray',
                        'Starter Plan' => 'info',
                        'Professional Plan' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trial' => 'info',
                        'pending' => 'warning',
                        'expired' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total_accommodations')
                    ->label('Total Accommodations')
                    ->getStateUsing(fn (Subscription $record): int => $record->total_accommodations)
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_in_rupees')
                    ->label('Price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable()
                    ->color(fn (Subscription $record): string => 
                        $record->current_period_end->isPast() ? 'danger' : 
                        ($record->current_period_end->diffInDays() <= 7 ? 'warning' : 'success')
                    ),
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Left')
                    ->getStateUsing(fn (Subscription $record): int => $record->days_remaining)
                    ->sortable()
                    ->color(fn (int $state): string => 
                        $state <= 0 ? 'danger' : 
                        ($state <= 7 ? 'warning' : 'success')
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'trial' => 'Trial',
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('plan_slug')
                    ->label('Plan')
                    ->options([
                        'trial' => 'Trial Plan',
                        'starter' => 'Starter Plan',
                        'professional' => 'Professional Plan',
                    ]),
                Tables\Filters\Filter::make('expiring_soon')
                    ->query(fn (Builder $query): Builder => $query->where('current_period_end', '<=', now()->addDays(7)))
                    ->label('Expiring Soon (7 days)'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('extend')
                    ->label('Extend')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->form([
                        Forms\Components\TextInput::make('months')
                            ->label('Months to extend')
                            ->numeric()
                            ->required()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(12),
                    ])
                    ->action(function (Subscription $record, array $data): void {
                        $subscriptionService = app(SubscriptionService::class);
                        $subscriptionService->extendSubscription($record, $data['months'], ['performed_by' => 'admin']);
                        
                        Notification::make()
                            ->title('Subscription Extended')
                            ->body("Subscription extended by {$data['months']} month(s)")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Subscription $record): bool => $record->status === 'active'),
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Subscription $record): void {
                        $subscriptionService = app(SubscriptionService::class);
                        $subscriptionService->cancelSubscription($record, ['performed_by' => 'admin']);
                        
                        Notification::make()
                            ->title('Subscription Cancelled')
                            ->body('Subscription has been cancelled')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Subscription $record): bool => $record->status === 'active'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('extend_bulk')
                        ->label('Extend Selected')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('months')
                                ->label('Months to extend')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1)
                                ->maxValue(12),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $subscriptionService = app(SubscriptionService::class);
                            $count = 0;
                            
                            foreach ($records as $record) {
                                if ($record->status === 'active') {
                                    $subscriptionService->extendSubscription($record, $data['months'], ['performed_by' => 'admin']);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Subscriptions Extended')
                                ->body("{$count} subscription(s) extended by {$data['months']} month(s)")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SubscriptionHistoryRelationManager::class,
            RelationManagers\SubscriptionAddonsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
