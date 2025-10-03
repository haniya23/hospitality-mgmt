<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LocationResource\Pages;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Get;
use Filament\Forms\Set;

class LocationResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'System Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $label = 'Location Management';

    protected static ?string $pluralLabel = 'Location Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Location Management')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Countries')
                            ->schema([
                                Forms\Components\Repeater::make('countries')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('code')
                                                    ->label('Country Code')
                                                    ->required()
                                                    ->maxLength(3)
                                                    ->placeholder('IN, US, UK'),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->addActionLabel('Add Country')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('States')
                            ->schema([
                                Forms\Components\Select::make('country_for_states')
                                    ->label('Select Country')
                                    ->options(Country::pluck('name', 'id'))
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set) => $set('states', []))
                                    ->columnSpanFull(),

                                Forms\Components\Repeater::make('states')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('code')
                                                    ->label('State Code')
                                                    ->maxLength(10)
                                                    ->placeholder('MH, KA, TN'),
                                            ]),
                                        Forms\Components\Hidden::make('country_id')
                                            ->default(fn (Get $get) => $get('country_for_states')),
                                    ])
                                    ->visible(fn (Get $get) => filled($get('country_for_states')))
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->addActionLabel('Add State')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Districts')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('country_for_districts')
                                            ->label('Select Country')
                                            ->options(Country::pluck('name', 'id'))
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('state_for_districts', null)),

                                        Forms\Components\Select::make('state_for_districts')
                                            ->label('Select State')
                                            ->options(fn (Get $get): array => 
                                                State::where('country_id', $get('country_for_districts'))
                                                    ->pluck('name', 'id')
                                                    ->all()
                                            )
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('districts', [])),
                                    ]),

                                Forms\Components\Repeater::make('districts')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Hidden::make('state_id')
                                            ->default(fn (Get $get) => $get('state_for_districts')),
                                    ])
                                    ->visible(fn (Get $get) => filled($get('state_for_districts')))
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->addActionLabel('Add District')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Cities')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Select::make('country_for_cities')
                                            ->label('Select Country')
                                            ->options(Country::pluck('name', 'id'))
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('state_for_cities', null)),

                                        Forms\Components\Select::make('state_for_cities')
                                            ->label('Select State')
                                            ->options(fn (Get $get): array => 
                                                State::where('country_id', $get('country_for_cities'))
                                                    ->pluck('name', 'id')
                                                    ->all()
                                            )
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('district_for_cities', null)),

                                        Forms\Components\Select::make('district_for_cities')
                                            ->label('Select District')
                                            ->options(fn (Get $get): array => 
                                                District::where('state_id', $get('state_for_cities'))
                                                    ->pluck('name', 'id')
                                                    ->all()
                                            )
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('cities', [])),
                                    ]),

                                Forms\Components\Repeater::make('cities')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Hidden::make('district_id')
                                            ->default(fn (Get $get) => $get('district_for_cities')),
                                    ])
                                    ->visible(fn (Get $get) => filled($get('district_for_cities')))
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->addActionLabel('Add City')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Pincodes')
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\Select::make('country_for_pincodes')
                                            ->label('Select Country')
                                            ->options(Country::pluck('name', 'id'))
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('state_for_pincodes', null)),

                                        Forms\Components\Select::make('state_for_pincodes')
                                            ->label('Select State')
                                            ->options(fn (Get $get): array => 
                                                State::where('country_id', $get('country_for_pincodes'))
                                                    ->pluck('name', 'id')
                                                    ->all()
                                            )
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('district_for_pincodes', null)),

                                        Forms\Components\Select::make('district_for_pincodes')
                                            ->label('Select District')
                                            ->options(fn (Get $get): array => 
                                                District::where('state_id', $get('state_for_pincodes'))
                                                    ->pluck('name', 'id')
                                                    ->all()
                                            )
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('city_for_pincodes', null)),

                                        Forms\Components\Select::make('city_for_pincodes')
                                            ->label('Select City')
                                            ->options(fn (Get $get): array => 
                                                City::where('district_id', $get('district_for_pincodes'))
                                                    ->pluck('name', 'id')
                                                    ->all()
                                            )
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('pincodes', [])),
                                    ]),

                                Forms\Components\Repeater::make('pincodes')
                                    ->schema([
                                        Forms\Components\TextInput::make('code')
                                            ->label('Pincode')
                                            ->required()
                                            ->maxLength(10)
                                            ->placeholder('400001, 110001'),
                                        Forms\Components\Hidden::make('city_id')
                                            ->default(fn (Get $get) => $get('city_for_pincodes')),
                                    ])
                                    ->visible(fn (Get $get) => filled($get('city_for_pincodes')))
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['code'] ?? null)
                                    ->addActionLabel('Add Pincode')
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Bulk Import')
                            ->schema([
                                Forms\Components\Section::make('Import Locations')
                                    ->description('Import locations from JSON files. Use the sample format below as a template.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('locations_json')
                                            ->label('Import All Locations')
                                            ->acceptedFileTypes(['application/json'])
                                            ->helperText('Upload a JSON file with the complete location hierarchy'),

                                        Forms\Components\ViewField::make('sample_format')
                                            ->view('admin.location-sample-format')
                                            ->viewData([
                                                'sampleData' => [
                                                    'countries' => [
                                                        [
                                                            'name' => 'India',
                                                            'code' => 'IN',
                                                            'states' => [
                                                                [
                                                                    'name' => 'Maharashtra',
                                                                    'code' => 'MH',
                                                                    'districts' => [
                                                                        [
                                                                            'name' => 'Mumbai',
                                                                            'cities' => [
                                                                                [
                                                                                    'name' => 'Mumbai',
                                                                                    'pincodes' => [
                                                                                        ['code' => '400001'],
                                                                                        ['code' => '400002'],
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ],
                                                                [
                                                                    'name' => 'Karnataka',
                                                                    'code' => 'KA',
                                                                    'districts' => [
                                                                        [
                                                                            'name' => 'Bangalore Urban',
                                                                            'cities' => [
                                                                                [
                                                                                    'name' => 'Bangalore',
                                                                                    'pincodes' => [
                                                                                        ['code' => '560001'],
                                                                                        ['code' => '560002'],
                                                                                    ]
                                                                                ]
                                                                            ]
                                                                        ]
                                                                    ]
                                                                ]
                                                            ]
                                                        ]
                                                    ]
                                                ]
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('code')
                    ->label('Code')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('states_count')
                    ->label('States')
                    ->counts('states')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('properties_count')
                    ->label('Properties')
                    ->state(function (Country $record) {
                        return \App\Models\PropertyLocation::whereHas('city.district.state', function ($query) use ($record) {
                            $query->where('country_id', $record->id);
                        })->count();
                    })
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('manage_states')
                    ->label('Manage States')
                    ->icon('heroicon-o-map-pin')
                    ->url(fn (Country $record) => static::getUrl('edit', ['record' => $record]))
                    ->color('info')
                    ->tooltip('Edit this country to manage its states'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Country Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->weight(FontWeight::Bold),
                                Infolists\Components\TextEntry::make('code')
                                    ->badge(),
                            ]),
                    ]),

                Infolists\Components\Section::make('Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('states_count')
                                    ->label('Total States')
                                    ->state(fn ($record) => $record->states()->count()),
                                Infolists\Components\TextEntry::make('districts_count')
                                    ->label('Total Districts')
                                    ->state(fn ($record) => District::whereHas('state', fn ($q) => $q->where('country_id', $record->id))->count()),
                                Infolists\Components\TextEntry::make('cities_count')
                                    ->label('Total Cities')
                                    ->state(fn ($record) => City::whereHas('district.state', fn ($q) => $q->where('country_id', $record->id))->count()),
                                Infolists\Components\TextEntry::make('pincodes_count')
                                    ->label('Total Pincodes')
                                    ->state(fn ($record) => Pincode::whereHas('city.district.state', fn ($q) => $q->where('country_id', $record->id))->count()),
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
            'index' => Pages\ListLocations::route('/'),
            'create' => Pages\CreateLocation::route('/create'),
            'view' => Pages\ViewLocation::route('/{record}'),
            'edit' => Pages\EditLocation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }
}
