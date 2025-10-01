<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionHistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'history';

    protected static ?string $title = 'Subscription History';

    protected static ?string $modelLabel = 'History Entry';

    protected static ?string $pluralModelLabel = 'History Entries';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('action')
            ->columns([
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
            ])
            ->headerActions([
                // No create action for history
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for history
            ])
            ->defaultSort('created_at', 'desc');
    }
}
