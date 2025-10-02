<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use App\Models\PropertyCategory;
use App\Models\User;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;
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

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Property Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Basic Information')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                            if ($operation !== 'create') {
                                                return;
                                            }
                                            $set('slug', \Illuminate\Support\Str::slug($state));
                                        }),

                                    Forms\Components\Select::make('property_category_id')
                                        ->label('Property Category')
                                        ->relationship('category', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('name')
                                                ->required()
                                                ->maxLength(255),
                                            Forms\Components\Textarea::make('description')
                                                ->maxLength(500),
                                        ])
                                        ->required(),
                                ]),

                            Forms\Components\Select::make('owner_id')
                                ->label('Property Owner')
                                ->relationship('owner', 'name')
                                ->searchable(['name', 'email'])
                                ->preload()
                                ->required()
                                ->default(auth()->id()),

                            Forms\Components\RichEditor::make('description')
                                ->required()
                                ->maxLength(2000)
                                ->columnSpanFull(),

                            Forms\Components\Select::make('status')
                                ->options([
                                    'draft' => 'Draft',
                                    'pending_approval' => 'Pending Approval',
                                    'approved' => 'Approved',
                                    'rejected' => 'Rejected',
                                    'active' => 'Active',
                                    'inactive' => 'Inactive',
                                ])
                                ->default('draft')
                                ->required(),

                            Forms\Components\TextInput::make('wizard_step_completed')
                                ->numeric()
                                ->default(1)
                                ->hidden(),
                        ]),

                    Wizard\Step::make('Location Details')
                        ->icon('heroicon-o-map-pin')
                        ->schema([
                            Forms\Components\Fieldset::make('Address Information')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('location.country_id')
                                                ->label('Country')
                                                ->relationship('location.country', 'name')
                                                ->searchable()
                                                ->preload()
                                                ->live()
                                                ->afterStateUpdated(fn (Set $set) => $set('location.state_id', null))
                                                ->required(),

                                            Forms\Components\Select::make('location.state_id')
                                                ->label('State')
                                                ->options(fn (Get $get): array => 
                                                    State::query()
                                                        ->where('country_id', $get('location.country_id'))
                                                        ->pluck('name', 'id')
                                                        ->all()
                                                )
                                                ->searchable()
                                                ->live()
                                                ->afterStateUpdated(fn (Set $set) => $set('location.district_id', null))
                                                ->required(),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('location.district_id')
                                                ->label('District')
                                                ->options(fn (Get $get): array => 
                                                    District::query()
                                                        ->where('state_id', $get('location.state_id'))
                                                        ->pluck('name', 'id')
                                                        ->all()
                                                )
                                                ->searchable()
                                                ->live()
                                                ->afterStateUpdated(fn (Set $set) => $set('location.city_id', null))
                                                ->required(),

                                            Forms\Components\Select::make('location.city_id')
                                                ->label('City')
                                                ->options(fn (Get $get): array => 
                                                    City::query()
                                                        ->where('district_id', $get('location.district_id'))
                                                        ->pluck('name', 'id')
                                                        ->all()
                                                )
                                                ->searchable()
                                                ->live()
                                                ->afterStateUpdated(fn (Set $set) => $set('location.pincode_id', null))
                                                ->required(),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Select::make('location.pincode_id')
                                                ->label('Pincode')
                                                ->options(fn (Get $get): array => 
                                                    Pincode::query()
                                                        ->where('city_id', $get('location.city_id'))
                                                        ->pluck('code', 'id')
                                                        ->all()
                                                )
                                                ->searchable()
                                                ->required(),

                                            Forms\Components\TextInput::make('location.address')
                                                ->label('Street Address')
                                                ->required()
                                                ->maxLength(500),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('location.latitude')
                                                ->numeric()
                                                ->step(0.000001)
                                                ->helperText('Optional: For map integration'),

                                            Forms\Components\TextInput::make('location.longitude')
                                                ->numeric()
                                                ->step(0.000001)
                                                ->helperText('Optional: For map integration'),
                                        ]),
                                ]),
                        ]),

                    Wizard\Step::make('Amenities & Features')
                        ->icon('heroicon-o-star')
                        ->schema([
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
                                ->label('Property Photos')
                                ->image()
                                ->multiple()
                                ->reorderable()
                                ->maxFiles(20)
                                ->imageEditor()
                                ->imageEditorAspectRatios([
                                    '16:9',
                                    '4:3',
                                    '1:1',
                                ])
                                ->directory('property-photos')
                                ->visibility('public')
                                ->columnSpanFull(),

                            Forms\Components\FileUpload::make('documents')
                                ->label('Property Documents')
                                ->multiple()
                                ->acceptedFileTypes(['application/pdf', 'image/*'])
                                ->maxFiles(10)
                                ->directory('property-documents')
                                ->visibility('private')
                                ->columnSpanFull(),
                        ]),

                    Wizard\Step::make('Policies & Rules')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Fieldset::make('Check-in/Check-out Policies')
                                ->schema([
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TimePicker::make('policy.check_in_time')
                                                ->label('Check-in Time')
                                                ->default('14:00'),

                                            Forms\Components\TimePicker::make('policy.check_out_time')
                                                ->label('Check-out Time')
                                                ->default('11:00'),
                                        ]),

                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\TextInput::make('policy.minimum_stay')
                                                ->label('Minimum Stay (nights)')
                                                ->numeric()
                                                ->default(1)
                                                ->minValue(1),

                                            Forms\Components\TextInput::make('policy.maximum_stay')
                                                ->label('Maximum Stay (nights)')
                                                ->numeric()
                                                ->minValue(1),
                                        ]),
                                ]),

                            Forms\Components\Fieldset::make('Cancellation Policy')
                                ->schema([
                                    Forms\Components\Select::make('policy.cancellation_policy')
                                        ->label('Cancellation Policy Type')
                                        ->options([
                                            'flexible' => 'Flexible',
                                            'moderate' => 'Moderate',
                                            'strict' => 'Strict',
                                            'super_strict' => 'Super Strict',
                                            'custom' => 'Custom',
                                        ])
                                        ->default('moderate')
                                        ->live(),

                                    Forms\Components\Textarea::make('policy.cancellation_details')
                                        ->label('Cancellation Details')
                                        ->visible(fn (Get $get) => $get('policy.cancellation_policy') === 'custom')
                                        ->maxLength(1000),
                                ]),

                            Forms\Components\Fieldset::make('House Rules')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
                                            Forms\Components\Toggle::make('policy.smoking_allowed')
                                                ->label('Smoking Allowed'),

                                            Forms\Components\Toggle::make('policy.pets_allowed')
                                                ->label('Pets Allowed'),

                                            Forms\Components\Toggle::make('policy.parties_allowed')
                                                ->label('Parties/Events Allowed'),
                                        ]),

                                    Forms\Components\Textarea::make('policy.additional_rules')
                                        ->label('Additional House Rules')
                                        ->maxLength(1000)
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    Wizard\Step::make('Review & Approval')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Forms\Components\Placeholder::make('review_info')
                                ->label('Property Review')
                                ->content('Please review all the information before submitting for approval.')
                                ->columnSpanFull(),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\DateTimePicker::make('approved_at')
                                        ->label('Approved At')
                                        ->disabled()
                                        ->visible(fn ($record) => $record && $record->approved_at),

                                    Forms\Components\Select::make('approved_by')
                                        ->label('Approved By')
                                        ->relationship('approver', 'name')
                                        ->disabled()
                                        ->visible(fn ($record) => $record && $record->approved_by),
                                ]),

                            Forms\Components\Textarea::make('rejection_reason')
                                ->label('Rejection Reason')
                                ->maxLength(500)
                                ->visible(fn ($record) => $record && $record->status === 'rejected')
                                ->disabled(),

                            Forms\Components\Textarea::make('admin_notes')
                                ->label('Admin Notes')
                                ->maxLength(1000)
                                ->helperText('Internal notes for admin use')
                                ->columnSpanFull(),
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

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Property $record): string => 
                        $record->location ? 
                        "{$record->location->city->name}, {$record->location->state->name}" : 
                        'Location not set'
                    ),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('owner.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'pending_approval',
                        'success' => 'approved',
                        'danger' => 'rejected',
                        'primary' => 'active',
                        'secondary' => 'inactive',
                    ]),

                Tables\Columns\TextColumn::make('accommodations_count')
                    ->label('Accommodations')
                    ->counts('accommodations')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('reservations_count')
                    ->label('Bookings')
                    ->counts('reservations')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('wizard_step_completed')
                    ->label('Setup Progress')
                    ->formatStateUsing(fn (string $state): string => "Step {$state}/6")
                    ->badge()
                    ->color(fn (string $state): string => match ((int) $state) {
                        6 => 'success',
                        4, 5 => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending_approval' => 'Pending Approval',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->multiple(),

                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('owner')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload(),

                Filter::make('location')
                    ->form([
                        Forms\Components\Select::make('state_id')
                            ->label('State')
                            ->relationship('location.state', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('city_id')
                            ->label('City')
                            ->relationship('location.city', 'name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['state_id'],
                                fn (Builder $query, $stateId): Builder => 
                                    $query->whereHas('location', fn ($q) => $q->where('state_id', $stateId))
                            )
                            ->when(
                                $data['city_id'],
                                fn (Builder $query, $cityId): Builder => 
                                    $query->whereHas('location', fn ($q) => $q->where('city_id', $cityId))
                            );
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Property $record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                    })
                    ->visible(fn (Property $record) => $record->status === 'pending_approval'),

                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->action(function (Property $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                    })
                    ->visible(fn (Property $record) => $record->status === 'pending_approval'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                if ($record->status === 'pending_approval') {
                                    $record->update([
                                        'status' => 'approved',
                                        'approved_at' => now(),
                                        'approved_by' => auth()->id(),
                                    ]);
                                }
                            });
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Property Overview')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('name')
                                    ->weight(FontWeight::Bold),
                                Infolists\Components\TextEntry::make('category.name')
                                    ->badge(),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'pending_approval' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'active' => 'primary',
                                        'inactive' => 'secondary',
                                        default => 'gray',
                                    }),
                            ]),
                        Infolists\Components\TextEntry::make('description')
                            ->html()
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Location')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('location.country.name')
                                    ->label('Country'),
                                Infolists\Components\TextEntry::make('location.state.name')
                                    ->label('State'),
                                Infolists\Components\TextEntry::make('location.district.name')
                                    ->label('District'),
                                Infolists\Components\TextEntry::make('location.city.name')
                                    ->label('City'),
                                Infolists\Components\TextEntry::make('location.pincode.code')
                                    ->label('Pincode'),
                                Infolists\Components\TextEntry::make('location.address')
                                    ->label('Address'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Management')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('owner.name')
                                    ->label('Owner'),
                                Infolists\Components\TextEntry::make('accommodations_count')
                                    ->label('Total Accommodations')
                                    ->state(fn ($record) => $record->accommodations()->count()),
                                Infolists\Components\TextEntry::make('reservations_count')
                                    ->label('Total Bookings')
                                    ->state(fn ($record) => $record->reservations()->count()),
                            ]),
                    ]),

                Infolists\Components\Section::make('Approval Status')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('approved_at')
                                    ->dateTime()
                                    ->placeholder('Not approved yet'),
                                Infolists\Components\TextEntry::make('approver.name')
                                    ->label('Approved By')
                                    ->placeholder('Not approved yet'),
                            ]),
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->visible(fn ($record) => $record->status === 'rejected')
                            ->columnSpanFull(),
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
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'view' => Pages\ViewProperty::route('/{record}'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending_approval')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
