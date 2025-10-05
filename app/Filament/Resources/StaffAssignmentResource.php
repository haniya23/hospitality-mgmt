<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StaffAssignmentResource\Pages;
use App\Filament\Resources\StaffAssignmentResource\RelationManagers;
use App\Models\StaffAssignment;
use App\Models\User;
use App\Models\Property;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Hash;

class StaffAssignmentResource extends Resource
{
    protected static ?string $model = StaffAssignment::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    
    protected static ?string $navigationGroup = 'Staff Management';
    
    protected static ?int $navigationSort = 1;
    
    protected static ?string $recordTitleAttribute = 'user.name';
    
    protected static ?string $label = 'Staff Member';
    
    protected static ?string $pluralLabel = 'Staff Members';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Staff Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Staff Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true),
                                    
                                Forms\Components\TextInput::make('mobile_number')
                                    ->label('Mobile Number')
                                    ->tel()
                                    ->required()
                                    ->unique(User::class, 'mobile_number', ignoreRecord: true)
                                    ->maxLength(15)
                                    ->live(onBlur: true),
                            ]),
                            
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique(User::class, 'email', ignoreRecord: true)
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('pin')
                            ->label('PIN Code')
                            ->numeric()
                            ->length(4)
                            ->default('0000')
                            ->required(),
                    ]),
                    
                Forms\Components\Section::make('Assignment Details')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('property_id')
                                    ->label('Property')
                                    ->options(Property::where('status', 'active')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live(),
                                    
                                Forms\Components\Select::make('role_id')
                                    ->label('Role')
                                    ->options(Role::pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        // Auto-set access based on role
                                        $role = Role::find($state);
                                        if ($role && strtolower($role->name) === 'manager') {
                                            $set('booking_access', true);
                                            $set('guest_service_access', true);
                                        } else {
                                            // For non-managers, default to no access
                                            $set('booking_access', false);
                                            $set('guest_service_access', false);
                                        }
                                    }),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('start_date')
                                    ->label('Start Date')
                                    ->required()
                                    ->default(now()),
                                    
                                Forms\Components\DatePicker::make('end_date')
                                    ->label('End Date')
                                    ->after('start_date'),
                            ]),
                            
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'suspended' => 'Suspended',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
                    
                Forms\Components\Section::make('Access Control')
                    ->description('Simple toggles for staff access. All staff can view upcoming bookings and guest services, but only those with access can edit them. Managers automatically get full access.')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('booking_access')
                                    ->label('Booking Access')
                                    ->helperText('Can edit booking details and reservations')
                                    ->default(false)
                                    ->live(),
                                    
                                Forms\Components\Toggle::make('guest_service_access')
                                    ->label('Guest Service Access')
                                    ->helperText('Can update guest services and handle requests')
                                    ->default(false)
                                    ->live(),
                            ]),
                            
                        Forms\Components\Placeholder::make('access_info')
                            ->label('')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle text-blue-500 mt-1"></i>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-semibold text-blue-800">How Access Works</h4>
                                            <div class="mt-2 text-sm text-blue-700">
                                                <ul class="list-disc list-inside space-y-1">
                                                    <li><strong>All staff</strong> can view upcoming bookings and guest services</li>
                                                    <li><strong>Booking Access:</strong> Allows editing booking details, check-in/check-out times, and guest information</li>
                                                    <li><strong>Guest Service Access:</strong> Allows updating guest service requests, room service, and guest communications</li>
                                                    <li>Perfect for cleaners (no access) vs front desk staff (both access)</li>
                                                    <li><strong>Managers:</strong> Automatically get both access levels</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Staff Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('user.mobile_number')
                    ->label('Mobile')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('role.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Manager' => 'success',
                        'Front Desk' => 'info',
                        'Housekeeping' => 'warning',
                        'Cleaner' => 'gray',
                        default => 'gray',
                    }),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => 'suspended',
                    ]),
                    
                Tables\Columns\IconColumn::make('booking_access')
                    ->label('Booking Access')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                    
                Tables\Columns\IconColumn::make('guest_service_access')
                    ->label('Guest Service Access')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date()
                    ->sortable()
                    ->placeholder('No end date'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'suspended' => 'Suspended',
                    ]),
                    
                Tables\Filters\SelectFilter::make('role_id')
                    ->label('Role')
                    ->relationship('role', 'name'),
                    
                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'name'),
                    
                Tables\Filters\TernaryFilter::make('booking_access')
                    ->label('Has Booking Access'),
                    
                Tables\Filters\TernaryFilter::make('guest_service_access')
                    ->label('Has Guest Service Access'),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStaffAssignments::route('/'),
            'create' => Pages\CreateStaffAssignment::route('/create'),
            'edit' => Pages\EditStaffAssignment::route('/{record}/edit'),
        ];
    }
}
