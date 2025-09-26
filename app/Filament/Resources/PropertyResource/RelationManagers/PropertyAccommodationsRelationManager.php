<?php

namespace App\Filament\Resources\PropertyResource\RelationManagers;

use App\Models\PredefinedAccommodationType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;

class PropertyAccommodationsRelationManager extends RelationManager
{
    protected static string $relationship = 'propertyAccommodations';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('predefined_accommodation_type_id')
                    ->label('Accommodation Type')
                    ->relationship('predefinedType', 'name')
                    ->searchable()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('custom_name', null)),
                Forms\Components\TextInput::make('custom_name')
                    ->label('Custom Name')
                    ->maxLength(255)
                    ->visible(fn (Get $get) => $get('predefined_accommodation_type_id')),
                Forms\Components\TextInput::make('max_occupancy')
                    ->label('Max Occupancy')
                    ->required()
                    ->numeric()
                    ->default(2)
                    ->minValue(1)
                    ->maxValue(20),
                Forms\Components\TextInput::make('base_price')
                    ->label('Base Price (₹)')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->minValue(0)
                    ->prefix('₹'),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('features')
                    ->label('Features')
                    ->rows(2)
                    ->placeholder('Enter features separated by commas')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('display_name')
            ->columns([
                Tables\Columns\TextColumn::make('display_name')
                    ->label('Accommodation')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('predefinedType.name')
                    ->label('Type')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('max_occupancy')
                    ->label('Max Guests')
                    ->numeric()
                    ->sortable()
                    ->suffix(' guests'),
                Tables\Columns\TextColumn::make('base_price')
                    ->label('Base Price')
                    ->money('INR')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reservations_count')
                    ->label('Bookings')
                    ->counts('reservations')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('predefined_accommodation_type_id')
                    ->label('Type')
                    ->relationship('predefinedType', 'name')
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
