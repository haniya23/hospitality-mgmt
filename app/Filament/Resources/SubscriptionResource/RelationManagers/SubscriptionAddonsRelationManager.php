<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionAddonsRelationManager extends RelationManager
{
    protected static string $relationship = 'addons';

    protected static ?string $title = 'Add-ons';

    protected static ?string $modelLabel = 'Add-on';

    protected static ?string $pluralModelLabel = 'Add-ons';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('qty')
                    ->label('Quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(50),
                Forms\Components\TextInput::make('unit_price_cents')
                    ->label('Unit Price (cents)')
                    ->required()
                    ->numeric()
                    ->default(9900),
                Forms\Components\DateTimePicker::make('cycle_start')
                    ->required(),
                Forms\Components\DateTimePicker::make('cycle_end')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('qty')
            ->columns([
                Tables\Columns\TextColumn::make('qty')
                    ->label('Quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price_in_rupees')
                    ->label('Unit Price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price_in_rupees')
                    ->label('Total Price')
                    ->money('INR')
                    ->getStateUsing(fn ($record) => $record->total_price_in_rupees),
                Tables\Columns\TextColumn::make('cycle_start')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cycle_end')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Add-ons are typically created through the API, not admin panel
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
