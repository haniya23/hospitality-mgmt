<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionAddonResource\Pages;
use App\Filament\Resources\SubscriptionAddonResource\RelationManagers;
use App\Models\SubscriptionAddon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionAddonResource extends Resource
{
    protected static ?string $model = SubscriptionAddon::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('subscription_id')
                    ->relationship('subscription', 'id')
                    ->required(),
                Forms\Components\TextInput::make('qty')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('unit_price_cents')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('cycle_start')
                    ->required(),
                Forms\Components\DateTimePicker::make('cycle_end')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subscription.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price_cents')
                    ->numeric()
                    ->sortable(),
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
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListSubscriptionAddons::route('/'),
            'create' => Pages\CreateSubscriptionAddon::route('/create'),
            'edit' => Pages\EditSubscriptionAddon::route('/{record}/edit'),
        ];
    }
}
