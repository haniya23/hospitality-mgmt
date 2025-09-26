<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyAccommodationResource\Pages;
use App\Filament\Resources\PropertyAccommodationResource\RelationManagers;
use App\Models\PropertyAccommodation;
use App\Models\Property;
use App\Models\PredefinedAccommodationType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;

class PropertyAccommodationResource extends Resource
{
    protected static ?string $model = PropertyAccommodation::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Accommodations';
    protected static ?string $navigationGroup = 'Property Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'name')
                    ->searchable()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('property_category_id')
                            ->relationship('category', 'name')
                            ->required(),
                        Forms\Components\Textarea::make('description'),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return Property::create([
                            'name' => $data['name'],
                            'property_category_id' => $data['property_category_id'],
                            'description' => $data['description'] ?? null,
                            'owner_id' => auth()->id(),
                            'status' => 'active',
                        ])->getKey();
                    }),
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('property.name')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->url(fn (PropertyAccommodation $record): string => route('filament.admin.resources.properties.edit', $record->property)),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'name')
                    ->searchable(),
                Tables\Filters\SelectFilter::make('predefined_accommodation_type_id')
                    ->label('Type')
                    ->relationship('predefinedType', 'name')
                    ->searchable(),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->actions([
                Tables\Actions\Action::make('view_property')
                    ->icon('heroicon-m-building-office')
                    ->color('info')
                    ->url(fn (PropertyAccommodation $record): string => route('filament.admin.resources.properties.edit', $record->property))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListPropertyAccommodations::route('/'),
            'create' => Pages\CreatePropertyAccommodation::route('/create'),
            'edit' => Pages\EditPropertyAccommodation::route('/{record}/edit'),
        ];
    }
}
