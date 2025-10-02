<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Reservation;
use App\Models\Guest;
use App\Models\PropertyAccommodation;
use App\Models\B2bPartner;
use App\Models\User;
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

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Bookings';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'confirmation_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Guest Information')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Forms\Components\Select::make('guest_id')
                                ->label('Guest')
                                ->relationship('guest', 'name')
                                ->searchable(['name', 'email', 'mobile_number'])
                                ->preload()
                                ->createOptionForm([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('phone')
                                        ->tel()
                                        ->maxLength(20),
                                    Forms\Components\TextInput::make('mobile_number')
                                        ->tel()
                                        ->required()
                                        ->maxLength(20),
                                    Forms\Components\DatePicker::make('date_of_birth'),
                                    Forms\Components\Select::make('gender')
                                        ->options([
                                            'male' => 'Male',
                                            'female' => 'Female',
                                            'other' => 'Other',
                                        ]),
                                    Forms\Components\Textarea::make('address')
                                        ->maxLength(500),
                                    Forms\Components\Select::make('id_type')
                                        ->options([
                                            'aadhar' => 'Aadhar Card',
                                            'passport' => 'Passport',
                                            'driving_license' => 'Driving License',
                                            'voter_id' => 'Voter ID',
                                            'pan' => 'PAN Card',
                                        ]),
                                    Forms\Components\TextInput::make('id_number')
                                        ->maxLength(50),
                                ])
                                ->required()
                                ->columnSpanFull(),
                        ]),

                    Wizard\Step::make('Accommodation & Dates')
                        ->icon('heroicon-o-home')
                        ->schema([
                            Forms\Components\Select::make('property_accommodation_id')
                                ->label('Accommodation')
                                ->relationship('accommodation', 'custom_name')
                                ->getOptionLabelFromRecordUsing(fn (Model $record) => 
                                    "{$record->property->name} - {$record->display_name} (₹{$record->base_price}/night)"
                                )
                                ->searchable(['custom_name'])
                                ->preload()
                                ->required()
                                ->columnSpanFull(),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\DatePicker::make('check_in_date')
                                        ->required()
                                        ->minDate(now())
                                        ->live()
                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                            $checkOut = $get('check_out_date');
                                            if ($checkOut && $state >= $checkOut) {
                                                $set('check_out_date', null);
                                            }
                                        }),

                                    Forms\Components\DatePicker::make('check_out_date')
                                        ->required()
                                        ->minDate(fn (Forms\Get $get) => 
                                            $get('check_in_date') ? 
                                            \Carbon\Carbon::parse($get('check_in_date'))->addDay() : 
                                            now()->addDay()
                                        )
                                        ->live(),
                                ]),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('adults')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->maxValue(10)
                                        ->required(),

                                    Forms\Components\TextInput::make('children')
                                        ->numeric()
                                        ->default(0)
                                        ->minValue(0)
                                        ->maxValue(10),
                                ]),
                        ]),

                    Wizard\Step::make('Pricing & Payment')
                        ->icon('heroicon-o-currency-rupee')
                        ->schema([
                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('total_amount')
                                        ->numeric()
                                        ->prefix('₹')
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                            $advance = $get('advance_paid') ?? 0;
                                            $set('balance_pending', max(0, $state - $advance));
                                        }),

                                    Forms\Components\TextInput::make('advance_paid')
                                        ->numeric()
                                        ->prefix('₹')
                                        ->default(0)
                                        ->live()
                                        ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                            $total = $get('total_amount') ?? 0;
                                            $set('balance_pending', max(0, $total - $state));
                                        }),
                                ]),

                            Forms\Components\TextInput::make('balance_pending')
                                ->numeric()
                                ->prefix('₹')
                                ->disabled()
                                ->dehydrated(),

                            Forms\Components\Grid::make(2)
                                ->schema([
                                    Forms\Components\TextInput::make('rate_override')
                                        ->numeric()
                                        ->prefix('₹')
                                        ->helperText('Override the base rate if needed'),

                                    Forms\Components\TextInput::make('override_reason')
                                        ->maxLength(255)
                                        ->helperText('Reason for rate override'),
                                ]),
                        ]),

                    Wizard\Step::make('Additional Details')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Select::make('b2b_partner_id')
                                ->label('B2B Partner')
                                ->relationship('b2bPartner', 'partner_name')
                                ->searchable()
                                ->preload()
                                ->helperText('Select if this is a B2B booking'),

                            Forms\Components\Select::make('status')
                                ->options([
                                    'pending' => 'Pending',
                                    'confirmed' => 'Confirmed',
                                    'checked_in' => 'Checked In',
                                    'checked_out' => 'Checked Out',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                    'no_show' => 'No Show',
                                ])
                                ->default('pending')
                                ->required(),

                            Forms\Components\Textarea::make('special_requests')
                                ->maxLength(1000)
                                ->rows(3),

                            Forms\Components\Textarea::make('notes')
                                ->maxLength(1000)
                                ->rows(3)
                                ->helperText('Internal notes (not visible to guest)'),

                            Forms\Components\Select::make('created_by')
                                ->label('Created By')
                                ->relationship('creator', 'name')
                                ->default(auth()->id())
                                ->required()
                                ->disabled()
                                ->dehydrated(),
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
                Tables\Columns\TextColumn::make('confirmation_number')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->copyable(),

                Tables\Columns\TextColumn::make('guest.name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Reservation $record): string => $record->guest->mobile_number ?? ''),

                Tables\Columns\TextColumn::make('accommodation.display_name')
                    ->label('Accommodation')
                    ->searchable()
                    ->description(fn (Reservation $record): string => $record->accommodation->property->name ?? ''),

                Tables\Columns\TextColumn::make('check_in_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_out_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->money('INR')
                    ->sortable()
                    ->description(fn (Reservation $record): string => 
                        "Advance: ₹{$record->advance_paid} | Balance: ₹{$record->balance_pending}"
                    ),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'checked_in',
                        'info' => 'checked_out',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                        'gray' => 'no_show',
                    ]),

                Tables\Columns\TextColumn::make('b2bPartner.partner_name')
                    ->label('B2B Partner')
                    ->placeholder('Direct Booking')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'checked_in' => 'Checked In',
                        'checked_out' => 'Checked Out',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show' => 'No Show',
                    ])
                    ->multiple(),

                Filter::make('check_in_date')
                    ->form([
                        DatePicker::make('check_in_from')
                            ->label('Check-in From'),
                        DatePicker::make('check_in_until')
                            ->label('Check-in Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['check_in_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('check_in_date', '>=', $date),
                            )
                            ->when(
                                $data['check_in_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('check_in_date', '<=', $date),
                            );
                    }),

                SelectFilter::make('property')
                    ->relationship('accommodation.property', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('b2b_partner')
                    ->relationship('b2bPartner', 'partner_name')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('check_in')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Reservation $record) => $record->checkIn())
                    ->visible(fn (Reservation $record) => $record->status === 'confirmed'),
                
                Tables\Actions\Action::make('check_out')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Reservation $record) => $record->checkOut())
                    ->visible(fn (Reservation $record) => $record->status === 'checked_in'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_confirmed')
                        ->label('Mark as Confirmed')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->markAsConfirmed();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Reservation Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('confirmation_number')
                                    ->weight(FontWeight::Bold)
                                    ->copyable(),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'checked_in' => 'primary',
                                        'checked_out' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'no_show' => 'gray',
                                        default => 'gray',
                                    }),
                                Infolists\Components\TextEntry::make('created_at')
                                    ->dateTime(),
                            ]),
                    ]),

                Infolists\Components\Section::make('Guest Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('guest.name'),
                                Infolists\Components\TextEntry::make('guest.email'),
                                Infolists\Components\TextEntry::make('guest.mobile_number'),
                                Infolists\Components\TextEntry::make('guest.id_type')
                                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state))),
                            ]),
                    ]),

                Infolists\Components\Section::make('Accommodation & Stay Details')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('accommodation.property.name')
                                    ->label('Property'),
                                Infolists\Components\TextEntry::make('accommodation.display_name')
                                    ->label('Accommodation'),
                                Infolists\Components\TextEntry::make('check_in_date')
                                    ->date(),
                                Infolists\Components\TextEntry::make('check_out_date')
                                    ->date(),
                                Infolists\Components\TextEntry::make('adults'),
                                Infolists\Components\TextEntry::make('children'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Financial Details')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('total_amount')
                                    ->money('INR'),
                                Infolists\Components\TextEntry::make('advance_paid')
                                    ->money('INR'),
                                Infolists\Components\TextEntry::make('balance_pending')
                                    ->money('INR'),
                            ]),
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('rate_override')
                                    ->money('INR')
                                    ->placeholder('No override'),
                                Infolists\Components\TextEntry::make('override_reason')
                                    ->placeholder('No reason provided'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Additional Information')
                    ->schema([
                        Infolists\Components\TextEntry::make('b2bPartner.partner_name')
                            ->label('B2B Partner')
                            ->placeholder('Direct Booking'),
                        Infolists\Components\TextEntry::make('special_requests')
                            ->placeholder('No special requests'),
                        Infolists\Components\TextEntry::make('notes')
                            ->placeholder('No notes'),
                        Infolists\Components\TextEntry::make('creator.name')
                            ->label('Created By'),
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
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'view' => Pages\ViewReservation::route('/{record}'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
