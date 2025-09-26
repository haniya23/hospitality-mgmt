<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Filament\Resources\PropertyResource\RelationManagers;
use App\Models\Property;
use App\Models\User;
use App\Models\PropertyCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Properties';
    protected static ?string $navigationGroup = 'Property Management';

    public static function getEloquentQuery(): Builder
    {
        // Admin can see all properties, regular users can only see their own
        if (auth()->user()->is_admin) {
            return parent::getEloquentQuery()->with(['location', 'policy']);
        }
        
        return parent::getEloquentQuery()->where('owner_id', auth()->id())->with(['location', 'policy']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('owner_id')
                    ->label('Owner')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->required()
                    ->disabled(fn () => !auth()->user()->is_admin)
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('mobile_number')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\Select::make('subscription_status')
                            ->options([
                                'trial' => 'Trial',
                                'starter' => 'Starter',
                                'professional' => 'Professional',
                            ])
                            ->default('trial')
                            ->required(),
                        Forms\Components\TextInput::make('properties_limit')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->maxValue(10)
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        return User::create([
                            'name' => $data['name'],
                            'mobile_number' => $data['mobile_number'],
                            'email' => $data['email'],
                            'subscription_status' => $data['subscription_status'],
                            'properties_limit' => $data['properties_limit'],
                            'is_trial_active' => $data['subscription_status'] === 'trial',
                        ])->getKey();
                    }),
                Forms\Components\Select::make('property_category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                
                // Location Section
                Forms\Components\Section::make('Location Details')
                    ->schema([
                        Forms\Components\Textarea::make('location.address')
                            ->label('Address')
                            ->rows(3)
                            ->columnSpanFull()
                            ->default(fn ($record) => $record?->location?->address),
                        Forms\Components\Select::make('location.country_id')
                            ->label('Country')
                            ->options(\App\Models\Country::pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->default(fn ($record) => $record?->location?->country_id)
                            ->afterStateUpdated(fn (Set $set) => $set('location.state_id', null)),
                        Forms\Components\Select::make('location.state_id')
                            ->label('State')
                            ->options(function (callable $get) {
                                $countryId = $get('location.country_id');
                                if (!$countryId) return [];
                                return \App\Models\State::where('country_id', $countryId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->reactive()
                            ->default(fn ($record) => $record?->location?->state_id)
                            ->afterStateUpdated(fn (Set $set) => $set('location.district_id', null)),
                        Forms\Components\Select::make('location.district_id')
                            ->label('District')
                            ->options(function (callable $get) {
                                $stateId = $get('location.state_id');
                                if (!$stateId) return [];
                                return \App\Models\District::where('state_id', $stateId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->reactive()
                            ->default(fn ($record) => $record?->location?->district_id)
                            ->afterStateUpdated(fn (Set $set) => $set('location.city_id', null)),
                        Forms\Components\Select::make('location.city_id')
                            ->label('City')
                            ->options(function (callable $get) {
                                $districtId = $get('location.district_id');
                                if (!$districtId) return [];
                                return \App\Models\City::where('district_id', $districtId)->pluck('name', 'id');
                            })
                            ->searchable()
                            ->reactive()
                            ->default(fn ($record) => $record?->location?->city_id)
                            ->afterStateUpdated(fn (Set $set) => $set('location.pincode_id', null)),
                        Forms\Components\Select::make('location.pincode_id')
                            ->label('Pincode')
                            ->options(function (callable $get) {
                                $cityId = $get('location.city_id');
                                if (!$cityId) return [];
                                return \App\Models\Pincode::where('city_id', $cityId)->pluck('code', 'id');
                            })
                            ->searchable()
                            ->default(fn ($record) => $record?->location?->pincode_id),
                        Forms\Components\TextInput::make('location.latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.000001)
                            ->default(fn ($record) => $record?->location?->latitude),
                        Forms\Components\TextInput::make('location.longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.000001)
                            ->default(fn ($record) => $record?->location?->longitude),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                // Policies Section
                Forms\Components\Section::make('Property Policies')
                    ->schema([
                        Forms\Components\TimePicker::make('policy.check_in_time')
                            ->label('Check-in Time')
                            ->default(fn ($record) => $record?->policy?->check_in_time),
                        Forms\Components\TimePicker::make('policy.check_out_time')
                            ->label('Check-out Time')
                            ->default(fn ($record) => $record?->policy?->check_out_time),
                        Forms\Components\Textarea::make('policy.cancellation_policy')
                            ->label('Cancellation Policy')
                            ->rows(3)
                            ->columnSpanFull()
                            ->default(fn ($record) => $record?->policy?->cancellation_policy),
                        Forms\Components\Textarea::make('policy.house_rules')
                            ->label('House Rules')
                            ->rows(3)
                            ->columnSpanFull()
                            ->default(fn ($record) => $record?->policy?->house_rules),
                    ])
                    ->columns(2)
                    ->collapsible(),
                
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('rejection_reason')
                    ->columnSpanFull()
                    ->rows(3)
                    ->visible(fn (Get $get) => $get('status') === 'rejected'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location.city.name')
                    ->label('City')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('location.city.district.state.name')
                    ->label('State')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'active' => 'success',
                        'rejected' => 'danger',
                        'inactive' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('property_accommodations_count')
                    ->label('Accommodations')
                    ->counts('propertyAccommodations')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'active' => 'Active',
                        'rejected' => 'Rejected',
                        'inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('accommodations')
                    ->icon('heroicon-m-home')
                    ->color('info')
                    ->url(fn (Property $record): string => route('filament.admin.resources.property-accommodations.index', ['property' => $record->id]))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Property $record) => $record->status === 'pending')
                    ->action(function (Property $record) {
                        $record->update([
                            'status' => 'active',
                            'approved_at' => now(),
                            'approved_by' => auth()->id(),
                        ]);
                        Notification::make()
                            ->title('Property approved successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn (Property $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (Property $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        Notification::make()
                            ->title('Property rejected')
                            ->success()
                            ->send();
                    }),
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
            RelationManagers\PropertyAccommodationsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
