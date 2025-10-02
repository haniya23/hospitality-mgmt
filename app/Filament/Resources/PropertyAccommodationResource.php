<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyAccommodationResource\Pages;
use App\Models\PropertyAccommodation;
use App\Models\Property;
use App\Models\PredefinedAccommodationType;
use App\Models\Amenity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Get;
use Filament\Forms\Set;

class PropertyAccommodationResource extends Resource
{
    protected static ?string $model = PropertyAccommodation::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'display_name';

    protected static ?string $label = 'Accommodation';

    protected static ?string $pluralLabel = 'Accommodations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Basic Information')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\Select::make('property_id')
                                ->label('Property')
                                ->relationship('property', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn (Set $set) => $set('custom_name', null)),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\Select::make('predefined_accommodation_type_id')
                                        ->label('Accommodation Type')
                                        ->relationship('predefinedType', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->maxLength(255),
                                            Forms\Components\Textarea::make('description')
                                                ->maxLength(500),
                                        ])
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            if ($state) {
                                                $type = PredefinedAccommodationType::find($state);
                                                if ($type && $type->name !== 'Custom') {
                                                    $property = Property::find($get('property_id'));
                                                    if ($property) {
                                                        $set('custom_name', "{$property->name} - {$type->name}");
                                                    }
                                                }
                                            }
                                        }),

                                    Forms\Components\TextInput::make('custom_name')
                                        ->label('Custom Name')
                                        ->maxLength(255)
                                        ->helperText('Override the default name if needed')
                                        ->live(),
                                ]),

                            Forms\Components\RichEditor::make('description')
                                ->maxLength(2000)
                                ->columnSpanFull(),

                            Forms\Components\Grid::make(3)
                                ->schema([
                                    Forms\Components\TextInput::make('max_occupancy')
                                        ->label('Maximum Occupancy')
                                        ->numeric()
                                        ->required()
                                        ->minValue(1)
                                        ->maxValue(20)
                                        ->default(2),

                                    Forms\Components\TextInput::make('base_price')
                                        ->label('Base Price per Night')
                                        ->numeric()
                                        ->prefix('₹')
                                        ->required()
                                        ->minValue(0)
                                        ->step(0.01),

                                    Forms\Components\TextInput::make('size')
                                        ->label('Size (sq ft)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->step(0.01)
                                        ->suffix('sq ft'),
                                ]),

                            Forms\Components\Toggle::make('is_active')
                                ->label('Active')
                                ->default(true)
                                ->helperText('Inactive accommodations won\'t be available for booking'),
                        ]),

                    Wizard\Step::make('Features & Amenities')
                        ->icon('heroicon-o-star')
                        ->schema([
                            Forms\Components\TagsInput::make('features')
                                ->label('Key Features')
                                ->placeholder('Add features like "Sea View", "Balcony", "Kitchen", etc.')
                                ->helperText('Press Enter to add each feature')
                                ->columnSpanFull(),

                            Forms\Components\CheckboxList::make('amenities')
                                ->relationship('amenities', 'name')
                                ->searchable()
                                ->bulkToggleable()
                                ->columns(3)
                                ->gridDirection('row')
                                ->columnSpanFull(),

                            Forms\Components\Repeater::make('custom_amenities')
                                ->label('Custom Amenities')
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\Textarea::make('description')
                                        ->maxLength(500),
                                ])
                                ->collapsible()
                                ->columnSpanFull(),
                        ]),

                    Wizard\Step::make('Photos & Media')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\FileUpload::make('photos')
                                ->label('Accommodation Photos')
                                ->image()
                                ->multiple()
                                ->reorderable()
                                ->maxFiles(15)
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->directory('accommodation-photos')
                                ->visibility('public')
                                ->helperText('Upload high-quality photos showcasing the accommodation')
                                ->columnSpanFull(),

                            Forms\Components\FileUpload::make('floor_plan')
                                ->label('Floor Plan')
                                ->image()
                                ->maxSize(5120)
                                ->directory('accommodation-floor-plans')
                                ->visibility('public')
                                ->helperText('Optional: Upload floor plan or layout diagram')
                                ->columnSpanFull(),
                        ]),

                    Wizard\Step::make('Pricing & Availability')
                        ->icon('heroicon-o-currency-rupee')
                        ->schema([
                            Forms\Components\Fieldset::make('Pricing Rules')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('weekend_price_multiplier')
                                                ->label('Weekend Price Multiplier')
                                                ->numeric()
                                                ->step(0.1)
                                                ->default(1.2)
                                                ->helperText('Multiply base price by this for weekends'),

                                            Forms\Components\TextInput::make('peak_season_multiplier')
                                                ->label('Peak Season Multiplier')
                                                ->numeric()
                                                ->step(0.1)
                                                ->default(1.5)
                                                ->helperText('Multiply base price by this for peak season'),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('minimum_stay')
                                                ->label('Minimum Stay (nights)')
                                                ->numeric()
                                                ->default(1)
                                                ->minValue(1),

                                            Forms\Components\TextInput::make('maximum_stay')
                                                ->label('Maximum Stay (nights)')
                                                ->numeric()
                                                ->minValue(1)
                                                ->helperText('Leave empty for no limit'),
                                        ]),
                                ]),

                            Forms\Components\Fieldset::make('Availability Settings')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\DatePicker::make('available_from')
                                                ->label('Available From')
                                                ->default(now())
                                                ->minDate(now()),

                                            Forms\Components\DatePicker::make('available_until')
                                                ->label('Available Until')
                                                ->helperText('Leave empty for indefinite availability'),
                                        ]),

                                    Forms\Components\CheckboxList::make('blocked_days')
                                        ->label('Blocked Days of Week')
                                        ->options([
                                            'monday' => 'Monday',
                                            'tuesday' => 'Tuesday',
                                            'wednesday' => 'Wednesday',
                                            'thursday' => 'Thursday',
                                            'friday' => 'Friday',
                                            'saturday' => 'Saturday',
                                            'sunday' => 'Sunday',
                                        ])
                                        ->columns(4)
                                        ->helperText('Select days when this accommodation is not available'),
                                ]),
                        ]),

                    Wizard\Step::make('House Rules & Policies')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Fieldset::make('Check-in/Check-out')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TimePicker::make('check_in_time')
                                                ->label('Check-in Time')
                                                ->default('15:00'),

                                            Forms\Components\TimePicker::make('check_out_time')
                                                ->label('Check-out Time')
                                                ->default('11:00'),
                                        ]),

                                    Forms\Components\Toggle::make('self_check_in')
                                        ->label('Self Check-in Available')
                                        ->helperText('Guests can check-in without staff assistance'),

                                    Forms\Components\Textarea::make('check_in_instructions')
                                        ->label('Check-in Instructions')
                                        ->maxLength(1000)
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Fieldset::make('House Rules')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\Toggle::make('smoking_allowed')
                                                ->label('Smoking Allowed'),

                                            Forms\Components\Toggle::make('pets_allowed')
                                                ->label('Pets Allowed'),

                                            Forms\Components\Toggle::make('parties_allowed')
                                                ->label('Parties/Events Allowed'),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Toggle::make('children_allowed')
                                                ->label('Children Allowed')
                                                ->default(true),

                                            Forms\Components\Toggle::make('additional_guests_allowed')
                                                ->label('Additional Guests Allowed'),
                                        ]),

                                    Forms\Components\Textarea::make('house_rules')
                                        ->label('Additional House Rules')
                                        ->maxLength(1000)
                                        ->rows(4)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ])
                ->columnSpanFull()
                ->persistStepInQueryString()
                ->submitAction(new \Filament\Actions\Action('submit'))
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photos')
                    ->label('Photo')
                    ->circular()
                    ->stacked()
                    ->limit(1)
                    ->limitedRemainingText(),

                Tables\Columns\TextColumn::make('display_name')
                    ->label('Accommodation')
                    ->searchable(['custom_name'])
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (PropertyAccommodation $record): string => 
                        $record->property->name ?? 'No property'
                    ),

                Tables\Columns\TextColumn::make('predefinedType.name')
                    ->label('Type')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('max_occupancy')
                    ->label('Max Guests')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Base Price')
                    ->money('INR')
                    ->sortable()
                    ->description(fn (PropertyAccommodation $record): string => 
                        $record->size ? "{$record->size} sq ft" : ''
                    ),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('reservations_count')
                    ->label('Bookings')
                    ->counts('reservations')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('property.owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('property')
                    ->relationship('property', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('predefined_type')
                    ->relationship('predefinedType', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),

                Filter::make('max_occupancy')
                    ->form([
                        Forms\Components\TextInput::make('min_occupancy')
                            ->label('Minimum Occupancy')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_occupancy_filter')
                            ->label('Maximum Occupancy')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_occupancy'],
                                fn (Builder $query, $occupancy): Builder => $query->where('max_occupancy', '>=', $occupancy),
                            )
                            ->when(
                                $data['max_occupancy_filter'],
                                fn (Builder $query, $occupancy): Builder => $query->where('max_occupancy', '<=', $occupancy),
                            );
                    }),

                Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->label('Minimum Price')
                            ->numeric()
                            ->prefix('₹'),
                        Forms\Components\TextInput::make('max_price')
                            ->label('Maximum Price')
                            ->numeric()
                            ->prefix('₹'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn (Builder $query, $price): Builder => $query->where('base_price', '>=', $price),
                            )
                            ->when(
                                $data['max_price'],
                                fn (Builder $query, $price): Builder => $query->where('base_price', '<=', $price),
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('toggle_active')
                    ->label(fn (PropertyAccommodation $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (PropertyAccommodation $record) => $record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn (PropertyAccommodation $record) => $record->is_active ? 'warning' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (PropertyAccommodation $record) => $record->update(['is_active' => !$record->is_active])),

                Tables\Actions\Action::make('view_bookings')
                    ->label('View Bookings')
                    ->icon('heroicon-o-calendar-days')
                    ->url(fn (PropertyAccommodation $record) => 
                        \App\Filament\Resources\ReservationResource::getUrl('index', ['tableFilters[accommodation][value]' => $record->id])
                    )
                    ->color('primary'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate_selected')
                        ->label('Activate Selected')
                        ->icon('heroicon-o-play')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),

                    Tables\Actions\BulkAction::make('deactivate_selected')
                        ->label('Deactivate Selected')
                        ->icon('heroicon-o-pause')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Accommodation Overview')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('display_name')
                                    ->label('Name')
                                    ->weight(FontWeight::Bold),
                                Infolists\Components\TextEntry::make('predefinedType.name')
                                    ->label('Type')
                                    ->badge(),
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                        Infolists\Components\TextEntry::make('description')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Property Details')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('property.name')
                                    ->label('Property'),
                                Infolists\Components\TextEntry::make('property.owner.name')
                                    ->label('Property Owner'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Specifications')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('max_occupancy')
                                    ->label('Maximum Occupancy')
                                    ->badge()
                                    ->color('success'),
                                Infolists\Components\TextEntry::make('base_price')
                                    ->label('Base Price per Night')
                                    ->money('INR'),
                                Infolists\Components\TextEntry::make('size')
                                    ->label('Size')
                                    ->formatStateUsing(fn ($state) => $state ? "{$state} sq ft" : 'Not specified'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Features & Amenities')
                    ->schema([
                        Infolists\Components\TextEntry::make('features')
                            ->label('Key Features')
                            ->badge()
                            ->separator(',')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('amenities.name')
                            ->label('Amenities')
                            ->badge()
                            ->separator(',')
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Booking Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('reservations_count')
                                    ->label('Total Bookings')
                                    ->state(fn ($record) => $record->reservations()->count()),
                                Infolists\Components\TextEntry::make('active_reservations_count')
                                    ->label('Active Bookings')
                                    ->state(fn ($record) => $record->reservations()->whereIn('status', ['confirmed', 'checked_in'])->count()),
                                Infolists\Components\TextEntry::make('revenue')
                                    ->label('Total Revenue')
                                    ->state(fn ($record) => '₹' . number_format($record->reservations()->where('status', 'completed')->sum('total_amount'), 2)),
                            ]),
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
            'index' => Pages\ListPropertyAccommodations::route('/'),
            'create' => Pages\CreatePropertyAccommodation::route('/create'),
            'view' => Pages\ViewPropertyAccommodation::route('/{record}'),
            'edit' => Pages\EditPropertyAccommodation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}
