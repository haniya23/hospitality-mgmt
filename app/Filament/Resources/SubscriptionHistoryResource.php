<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionHistoryResource\Pages;
use App\Filament\Resources\SubscriptionHistoryResource\RelationManagers;
use App\Models\SubscriptionHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionHistoryResource extends Resource
{
    protected static ?string $model = SubscriptionHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    
    protected static ?string $navigationGroup = 'Subscriptions';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subscription_id')
                    ->relationship('subscription', 'plan_name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('data')
                    ->label('Data (JSON)')
                    ->rows(3),
                Forms\Components\TextInput::make('performed_by')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscription.user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subscription.plan_name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Trial Plan' => 'gray',
                        'Starter Plan' => 'info',
                        'Professional Plan' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'upgraded' => 'info',
                        'addon_added' => 'warning',
                        'cancelled' => 'danger',
                        'extended' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('performed_by')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'user' => 'info',
                        'system' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('data')
                    ->label('Details')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return collect($state)->map(function ($value, $key) {
                                return "{$key}: {$value}";
                            })->join(', ');
                        }
                        return $state;
                    })
                    ->limit(50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'created' => 'Created',
                        'upgraded' => 'Upgraded',
                        'addon_added' => 'Add-on Added',
                        'cancelled' => 'Cancelled',
                        'extended' => 'Extended',
                    ]),
                Tables\Filters\SelectFilter::make('performed_by')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                        'system' => 'System',
                    ]),
                Tables\Filters\Filter::make('recent')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(30)))
                    ->label('Last 30 days'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for audit trail
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptionHistories::route('/'),
            // No create/edit pages for audit trail
        ];
    }
}
