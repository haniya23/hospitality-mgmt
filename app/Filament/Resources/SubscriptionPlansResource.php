<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionPlansResource\Pages;
use App\Models\Subscription;
use App\Models\SubscriptionAddon;
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
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Colors\Color;

class SubscriptionPlansResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Subscription Management';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'plan_name';

    protected static ?string $label = 'Subscription Plans';

    protected static ?string $pluralLabel = 'Subscription Plans';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Subscription Management')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Plan Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('user_id')
                                            ->label('User')
                                            ->relationship('user', 'name')
                                            ->searchable(['name', 'email'])
                                            ->preload()
                                            ->required(),

                                        Forms\Components\Select::make('plan_slug')
                                            ->label('Plan Type')
                                            ->options([
                                                'trial' => 'Trial Plan',
                                                'starter' => 'Starter Plan',
                                                'professional' => 'Professional Plan',
                                            ])
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                $planDetails = match($state) {
                                                    'trial' => [
                                                        'plan_name' => 'Trial Plan',
                                                        'base_accommodation_limit' => 2,
                                                        'price_cents' => 0,
                                                        'billing_interval' => 'monthly',
                                                    ],
                                                    'starter' => [
                                                        'plan_name' => 'Starter Plan',
                                                        'base_accommodation_limit' => 5,
                                                        'price_cents' => 99900, // ₹999
                                                        'billing_interval' => 'monthly',
                                                    ],
                                                    'professional' => [
                                                        'plan_name' => 'Professional Plan',
                                                        'base_accommodation_limit' => 15,
                                                        'price_cents' => 199900, // ₹1999
                                                        'billing_interval' => 'monthly',
                                                    ],
                                                    default => [],
                                                };

                                                foreach ($planDetails as $key => $value) {
                                                    $set($key, $value);
                                                }
                                            }),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('plan_name')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\Select::make('status')
                                            ->options([
                                                'active' => 'Active',
                                                'inactive' => 'Inactive',
                                                'cancelled' => 'Cancelled',
                                                'expired' => 'Expired',
                                                'pending' => 'Pending',
                                            ])
                                            ->default('active')
                                            ->required(),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('base_accommodation_limit')
                                            ->label('Base Accommodations')
                                            ->numeric()
                                            ->required()
                                            ->minValue(0),

                                        Forms\Components\TextInput::make('addon_count')
                                            ->label('Addon Accommodations')
                                            ->numeric()
                                            ->default(0)
                                            ->minValue(0)
                                            ->helperText('Additional accommodations beyond base limit'),

                                        Forms\Components\TextInput::make('total_accommodations')
                                            ->label('Total Accommodations')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->live()
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, Get $get) {
                                                $base = $get('base_accommodation_limit') ?? 0;
                                                $addon = $get('addon_count') ?? 0;
                                                $component->state($base + $addon);
                                            }),
                                    ]),

                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('price_cents')
                                            ->label('Price (in cents)')
                                            ->numeric()
                                            ->required()
                                            ->helperText('Price in cents (e.g., 99900 for ₹999)')
                                            ->live()
                                            ->afterStateUpdated(function ($state, Set $set) {
                                                $set('display_price', '₹' . number_format($state / 100, 2));
                                            }),

                                        Forms\Components\TextInput::make('display_price')
                                            ->label('Display Price')
                                            ->disabled()
                                            ->dehydrated(false),

                                        Forms\Components\Select::make('billing_interval')
                                            ->options([
                                                'monthly' => 'Monthly',
                                                'quarterly' => 'Quarterly',
                                                'yearly' => 'Yearly',
                                            ])
                                            ->default('monthly')
                                            ->required(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\DateTimePicker::make('start_at')
                                            ->label('Start Date')
                                            ->default(now())
                                            ->required(),

                                        Forms\Components\DateTimePicker::make('current_period_end')
                                            ->label('Current Period End')
                                            ->required()
                                            ->live()
                                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                                $startDate = $get('start_at');
                                                if ($startDate && $state) {
                                                    $start = \Carbon\Carbon::parse($startDate);
                                                    $end = \Carbon\Carbon::parse($state);
                                                    $set('days_remaining', $end->diffInDays($start, false));
                                                }
                                            }),
                                    ]),

                                Forms\Components\TextInput::make('currency')
                                    ->default('INR')
                                    ->required()
                                    ->maxLength(3),

                                Forms\Components\TextInput::make('cashfree_order_id')
                                    ->label('Cashfree Order ID')
                                    ->maxLength(255)
                                    ->helperText('Payment gateway order ID'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Add-ons Management')
                            ->icon('heroicon-o-plus-circle')
                            ->schema([
                                Forms\Components\Repeater::make('addons')
                                    ->relationship('addons')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('qty')
                                                    ->label('Quantity')
                                                    ->numeric()
                                                    ->required()
                                                    ->minValue(1)
                                                    ->default(1),

                                                Forms\Components\TextInput::make('unit_price_cents')
                                                    ->label('Unit Price (cents)')
                                                    ->numeric()
                                                    ->required()
                                                    ->helperText('Price per unit in cents')
                                                    ->live()
                                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                                        $qty = $get('qty') ?? 1;
                                                        $set('total_price_display', '₹' . number_format(($state * $qty) / 100, 2));
                                                    }),

                                                Forms\Components\TextInput::make('total_price_display')
                                                    ->label('Total Price')
                                                    ->disabled()
                                                    ->dehydrated(false),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DateTimePicker::make('cycle_start')
                                                    ->label('Cycle Start')
                                                    ->default(now())
                                                    ->required(),

                                                Forms\Components\DateTimePicker::make('cycle_end')
                                                    ->label('Cycle End')
                                                    ->required(),
                                            ]),
                                    ])
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => 
                                        isset($state['qty']) && isset($state['unit_price_cents']) ? 
                                        "Addon: {$state['qty']} units @ ₹" . number_format($state['unit_price_cents'] / 100, 2) : 
                                        'New Addon'
                                    )
                                    ->addActionLabel('Add Addon')
                                    ->columnSpanFull(),

                                Forms\Components\Placeholder::make('addon_summary')
                                    ->label('Add-on Summary')
                                    ->content(function (Get $get) {
                                        $addons = $get('addons') ?? [];
                                        $totalQty = array_sum(array_column($addons, 'qty'));
                                        $totalAmount = array_sum(array_map(function ($addon) {
                                            return ($addon['qty'] ?? 0) * ($addon['unit_price_cents'] ?? 0);
                                        }, $addons));
                                        
                                        return "Total Add-on Accommodations: {$totalQty} | Total Add-on Cost: ₹" . number_format($totalAmount / 100, 2);
                                    })
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Plan Analytics')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Section::make('Usage Statistics')
                                    ->schema([
                                        Forms\Components\Grid::make(4)
                                            ->schema([
                                                Forms\Components\Placeholder::make('accommodations_used')
                                                    ->label('Accommodations Used')
                                                    ->content(function ($record) {
                                                        if (!$record) return '0';
                                                        $used = $record->user->propertyAccommodations()->count();
                                                        $total = $record->total_accommodations;
                                                        return "{$used} / {$total}";
                                                    }),

                                                Forms\Components\Placeholder::make('usage_percentage')
                                                    ->label('Usage Percentage')
                                                    ->content(function ($record) {
                                                        if (!$record) return '0%';
                                                        $used = $record->user->propertyAccommodations()->count();
                                                        $total = $record->total_accommodations;
                                                        $percentage = $total > 0 ? round(($used / $total) * 100, 1) : 0;
                                                        return "{$percentage}%";
                                                    }),

                                                Forms\Components\Placeholder::make('days_remaining')
                                                    ->label('Days Remaining')
                                                    ->content(function ($record) {
                                                        if (!$record) return '0';
                                                        return $record->days_remaining . ' days';
                                                    }),

                                                Forms\Components\Placeholder::make('subscription_status')
                                                    ->label('Status')
                                                    ->content(function ($record) {
                                                        if (!$record) return 'New';
                                                        return $record->isActive() ? 'Active' : 'Inactive';
                                                    }),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Revenue Information')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Placeholder::make('base_revenue')
                                                    ->label('Base Plan Revenue')
                                                    ->content(function ($record) {
                                                        if (!$record) return '₹0.00';
                                                        return '₹' . number_format($record->price, 2);
                                                    }),

                                                Forms\Components\Placeholder::make('addon_revenue')
                                                    ->label('Add-on Revenue')
                                                    ->content(function ($record) {
                                                        if (!$record) return '₹0.00';
                                                        return '₹' . number_format($record->total_addon_amount, 2);
                                                    }),

                                                Forms\Components\Placeholder::make('total_revenue')
                                                    ->label('Total Revenue')
                                                    ->content(function ($record) {
                                                        if (!$record) return '₹0.00';
                                                        return '₹' . number_format($record->total_subscription_amount, 2);
                                                    }),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Payment History')
                                    ->schema([
                                        Forms\Components\Placeholder::make('payment_history')
                                            ->label('Recent Payments')
                                            ->content(function ($record) {
                                                if (!$record) return 'No payments yet';
                                                $payments = $record->payments()->latest()->limit(5)->get();
                                                if ($payments->isEmpty()) return 'No payments found';
                                                
                                                $html = '<ul>';
                                                foreach ($payments as $payment) {
                                                    $html .= "<li>₹{$payment->amount} - {$payment->status} - {$payment->created_at->format('M d, Y')}</li>";
                                                }
                                                $html .= '</ul>';
                                                return $html;
                                            })
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Plan Templates')
                            ->icon('heroicon-o-document-duplicate')
                            ->schema([
                                Forms\Components\Section::make('Quick Plan Setup')
                                    ->description('Use these templates to quickly set up common subscription plans')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\Actions::make([
                                                    Forms\Components\Actions\Action::make('setup_trial')
                                                        ->label('Setup Trial Plan')
                                                        ->icon('heroicon-o-gift')
                                                        ->color('info')
                                                        ->action(function (Set $set) {
                                                            $set('plan_slug', 'trial');
                                                            $set('plan_name', 'Trial Plan');
                                                            $set('base_accommodation_limit', 2);
                                                            $set('price_cents', 0);
                                                            $set('billing_interval', 'monthly');
                                                            $set('current_period_end', now()->addDays(14));
                                                        }),

                                                    Forms\Components\Actions\Action::make('setup_starter')
                                                        ->label('Setup Starter Plan')
                                                        ->icon('heroicon-o-rocket-launch')
                                                        ->color('success')
                                                        ->action(function (Set $set) {
                                                            $set('plan_slug', 'starter');
                                                            $set('plan_name', 'Starter Plan');
                                                            $set('base_accommodation_limit', 5);
                                                            $set('price_cents', 99900);
                                                            $set('billing_interval', 'monthly');
                                                            $set('current_period_end', now()->addMonth());
                                                        }),

                                                    Forms\Components\Actions\Action::make('setup_professional')
                                                        ->label('Setup Professional Plan')
                                                        ->icon('heroicon-o-star')
                                                        ->color('warning')
                                                        ->action(function (Set $set) {
                                                            $set('plan_slug', 'professional');
                                                            $set('plan_name', 'Professional Plan');
                                                            $set('base_accommodation_limit', 15);
                                                            $set('price_cents', 199900);
                                                            $set('billing_interval', 'monthly');
                                                            $set('current_period_end', now()->addMonth());
                                                        }),
                                                ])
                                                ->columnSpanFull(),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Add-on Templates')
                                    ->schema([
                                        Forms\Components\Placeholder::make('addon_templates')
                                            ->label('Common Add-on Configurations')
                                            ->content('
                                                <div class="space-y-2">
                                                    <p><strong>Extra Accommodations:</strong> ₹199 per accommodation per month</p>
                                                    <p><strong>Bulk Add-on (5 units):</strong> ₹899 per month (₹179 per unit)</p>
                                                    <p><strong>Bulk Add-on (10 units):</strong> ₹1699 per month (₹169 per unit)</p>
                                                    <p><strong>Enterprise Add-on (20+ units):</strong> ₹149 per unit per month</p>
                                                </div>
                                            ')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Subscription $record): string => $record->user->email ?? ''),

                Tables\Columns\TextColumn::make('plan_name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn (Subscription $record): string => 
                        "Base: {$record->base_accommodation_limit} | Add-ons: {$record->addon_count}"
                    ),

                Tables\Columns\BadgeColumn::make('plan_slug')
                    ->label('Plan Type')
                    ->colors([
                        'info' => 'trial',
                        'success' => 'starter',
                        'warning' => 'professional',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'active',
                        'danger' => 'cancelled',
                        'warning' => 'expired',
                        'gray' => 'inactive',
                        'info' => 'pending',
                    ]),

                Tables\Columns\TextColumn::make('total_accommodations')
                    ->label('Total Accommodations')
                    ->badge()
                    ->color('primary')
                    ->state(fn (Subscription $record): int => $record->total_accommodations),

                Tables\Columns\TextColumn::make('price')
                    ->label('Monthly Price')
                    ->money('INR')
                    ->sortable()
                    ->description(fn (Subscription $record): string => 
                        $record->total_addon_amount > 0 ? 
                        "Add-ons: ₹" . number_format($record->total_addon_amount, 2) : 
                        'No add-ons'
                    ),

                Tables\Columns\TextColumn::make('current_period_end')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->description(fn (Subscription $record): string => 
                        $record->days_remaining . ' days remaining'
                    )
                    ->color(fn (Subscription $record): string => 
                        $record->days_remaining <= 7 ? 'danger' : 
                        ($record->days_remaining <= 30 ? 'warning' : 'success')
                    ),

                Tables\Columns\TextColumn::make('billing_interval')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->state(fn (Subscription $record): bool => $record->isActive())
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('plan_slug')
                    ->label('Plan Type')
                    ->options([
                        'trial' => 'Trial',
                        'starter' => 'Starter',
                        'professional' => 'Professional',
                    ])
                    ->multiple(),

                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'cancelled' => 'Cancelled',
                        'expired' => 'Expired',
                        'pending' => 'Pending',
                    ])
                    ->multiple(),

                SelectFilter::make('billing_interval')
                    ->options([
                        'monthly' => 'Monthly',
                        'quarterly' => 'Quarterly',
                        'yearly' => 'Yearly',
                    ])
                    ->multiple(),

                Filter::make('expiring_soon')
                    ->label('Expiring Soon (30 days)')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('current_period_end', '<=', now()->addDays(30))
                              ->where('current_period_end', '>', now())
                    )
                    ->toggle(),

                Filter::make('expired')
                    ->label('Expired')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('current_period_end', '<', now())
                    )
                    ->toggle(),

                Filter::make('has_addons')
                    ->label('Has Add-ons')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('addon_count', '>', 0)
                    )
                    ->toggle(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('renew')
                    ->label('Renew')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('renewal_period')
                            ->label('Renewal Period')
                            ->options([
                                '1' => '1 Month',
                                '3' => '3 Months',
                                '6' => '6 Months',
                                '12' => '12 Months',
                            ])
                            ->default('1')
                            ->required(),
                    ])
                    ->action(function (Subscription $record, array $data) {
                        $months = (int) $data['renewal_period'];
                        $record->update([
                            'current_period_end' => $record->current_period_end->addMonths($months),
                            'status' => 'active',
                        ]);
                    })
                    ->requiresConfirmation(),

                Tables\Actions\Action::make('add_addon')
                    ->label('Add Add-on')
                    ->icon('heroicon-o-plus-circle')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('qty')
                            ->label('Quantity')
                            ->numeric()
                            ->required()
                            ->minValue(1)
                            ->default(1),
                        Forms\Components\TextInput::make('unit_price_cents')
                            ->label('Unit Price (cents)')
                            ->numeric()
                            ->required()
                            ->default(19900), // ₹199
                    ])
                    ->action(function (Subscription $record, array $data) {
                        $record->addons()->create([
                            'qty' => $data['qty'],
                            'unit_price_cents' => $data['unit_price_cents'],
                            'cycle_start' => now(),
                            'cycle_end' => $record->current_period_end,
                        ]);
                        
                        $record->increment('addon_count', $data['qty']);
                    }),

                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Subscription $record) => $record->update(['status' => 'cancelled']))
                    ->visible(fn (Subscription $record) => $record->status === 'active'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulk_renew')
                        ->label('Bulk Renew')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->form([
                            Forms\Components\Select::make('renewal_period')
                                ->label('Renewal Period')
                                ->options([
                                    '1' => '1 Month',
                                    '3' => '3 Months',
                                    '6' => '6 Months',
                                    '12' => '12 Months',
                                ])
                                ->default('1')
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            $months = (int) $data['renewal_period'];
                            $records->each(function ($record) use ($months) {
                                $record->update([
                                    'current_period_end' => $record->current_period_end->addMonths($months),
                                    'status' => 'active',
                                ]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Subscription Overview')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('plan_name')
                                    ->weight(FontWeight::Bold),
                                Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'cancelled' => 'danger',
                                        'expired' => 'warning',
                                        'inactive' => 'gray',
                                        'pending' => 'info',
                                        default => 'gray',
                                    }),
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Currently Active')
                                    ->boolean()
                                    ->state(fn ($record) => $record->isActive()),
                            ]),
                    ]),

                Infolists\Components\Section::make('User Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('user.name')
                                    ->label('User'),
                                Infolists\Components\TextEntry::make('user.email')
                                    ->label('Email'),
                            ]),
                    ]),

                Infolists\Components\Section::make('Plan Details')
                    ->schema([
                        Infolists\Components\Grid::make(4)
                            ->schema([
                                Infolists\Components\TextEntry::make('base_accommodation_limit')
                                    ->label('Base Accommodations'),
                                Infolists\Components\TextEntry::make('addon_count')
                                    ->label('Add-on Accommodations'),
                                Infolists\Components\TextEntry::make('total_accommodations')
                                    ->label('Total Accommodations')
                                    ->badge()
                                    ->color('primary'),
                                Infolists\Components\TextEntry::make('billing_interval')
                                    ->badge(),
                            ]),
                    ]),

                Infolists\Components\Section::make('Pricing & Billing')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('price')
                                    ->label('Base Price')
                                    ->money('INR'),
                                Infolists\Components\TextEntry::make('total_addon_amount')
                                    ->label('Add-on Amount')
                                    ->money('INR'),
                                Infolists\Components\TextEntry::make('total_subscription_amount')
                                    ->label('Total Amount')
                                    ->money('INR')
                                    ->weight(FontWeight::Bold),
                            ]),
                    ]),

                Infolists\Components\Section::make('Subscription Period')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('start_at')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('current_period_end')
                                    ->dateTime(),
                                Infolists\Components\TextEntry::make('days_remaining')
                                    ->label('Days Remaining')
                                    ->badge()
                                    ->color(fn ($record): string => 
                                        $record->days_remaining <= 7 ? 'danger' : 
                                        ($record->days_remaining <= 30 ? 'warning' : 'success')
                                    ),
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
            'index' => Pages\ListSubscriptionPlans::route('/'),
            'create' => Pages\CreateSubscriptionPlans::route('/create'),
            'view' => Pages\ViewSubscriptionPlans::route('/{record}'),
            'edit' => Pages\EditSubscriptionPlans::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        // Check both Subscription model and User trial subscriptions
        $expiringSoon = static::getModel()::where('current_period_end', '<=', now()->addDays(7))
                                         ->where('current_period_end', '>', now())
                                         ->where('status', 'active')
                                         ->count();
        
        // Also count trial users expiring soon
        $trialUsersExpiring = \App\Models\User::where('subscription_status', 'trial')
                                            ->where('trial_ends_at', '<=', now()->addDays(7))
                                            ->where('trial_ends_at', '>', now())
                                            ->where('is_trial_active', true)
                                            ->count();
        
        $total = $expiringSoon + $trialUsersExpiring;
        return $total > 0 ? (string) $total : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
