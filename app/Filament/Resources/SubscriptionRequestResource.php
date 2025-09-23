<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionRequestResource\Pages;
use App\Models\SubscriptionRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class SubscriptionRequestResource extends Resource
{
    protected static ?string $model = SubscriptionRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Subscription Requests';
    protected static ?string $navigationGroup = 'Subscription Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('requested_plan')
                    ->options([
                        'starter' => 'Starter',
                        'professional' => 'Professional',
                    ])
                    ->required(),
                Forms\Components\Select::make('billing_cycle')
                    ->options([
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Request ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.user_id')
                    ->label('User ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.mobile_number')
                    ->label('Mobile')
                    ->searchable(),
                Tables\Columns\TextColumn::make('requested_plan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'starter' => 'success',
                        'professional' => 'primary',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('billing_cycle')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (SubscriptionRequest $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Select::make('duration')
                            ->label('Duration (Months)')
                            ->options([
                                '1' => '1 Month',
                                '3' => '3 Months',
                                '6' => '6 Months',
                                '12' => '12 Months',
                            ])
                            ->default('12')
                            ->required(),
                    ])
                    ->action(function (SubscriptionRequest $record, array $data) {
                        $user = $record->user;
                        $duration = (int) $data['duration'];
                        
                        // Fix SQL error by ensuring proper data types
                        $user->subscription_status = $record->requested_plan;
                        $user->properties_limit = $record->requested_plan === 'professional' ? 5 : 3;
                        $user->subscription_ends_at = now()->addMonths($duration);
                        $user->is_trial_active = false;
                        $user->save();
                        
                        // Process referral reward if user was referred and subscription is 3+ months
                        if ($user->referred_by && $duration >= 3) {
                            $referral = Referral::where('referred_id', $user->id)->first();
                            if ($referral && $referral->status === 'pending') {
                                $referral->update([
                                    'status' => 'completed',
                                    'completed_at' => now(),
                                ]);
                            }
                        }
                        
                        $record->update([
                            'status' => 'approved',
                            'admin_notes' => 'Approved for ' . $duration . ' months'
                        ]);
                        
                        Notification::make()
                            ->title('Subscription approved successfully')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn (SubscriptionRequest $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (SubscriptionRequest $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['reason']
                        ]);
                        
                        Notification::make()
                            ->title('Subscription request rejected')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptionRequests::route('/'),
            'create' => Pages\CreateSubscriptionRequest::route('/create'),
            'edit' => Pages\EditSubscriptionRequest::route('/{record}/edit'),
        ];
    }
}
