<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralWithdrawalResource\Pages;
use App\Models\ReferralWithdrawal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class ReferralWithdrawalResource extends Resource
{
    protected static ?string $model = ReferralWithdrawal::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Withdrawals';
    protected static ?string $navigationGroup = 'Referral Program';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->prefix('₹')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'paid' => 'Paid',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('admin_notes')
                    ->rows(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.mobile_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('INR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'paid' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('requested_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'paid' => 'Paid',
                    ]),
            ])
            ->actions([
                Action::make('approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->visible(fn (ReferralWithdrawal $record) => $record->status === 'pending')
                    ->action(function (ReferralWithdrawal $record) {
                        $record->update([
                            'status' => 'approved',
                            'processed_at' => now(),
                        ]);
                        Notification::make()->title('Withdrawal approved')->success()->send();
                    }),
                Action::make('reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->visible(fn (ReferralWithdrawal $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->required(),
                    ])
                    ->action(function (ReferralWithdrawal $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'admin_notes' => $data['reason'],
                            'processed_at' => now(),
                        ]);
                        Notification::make()->title('Withdrawal rejected')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('requested_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferralWithdrawals::route('/'),
            'create' => Pages\CreateReferralWithdrawal::route('/create'),
            'edit' => Pages\EditReferralWithdrawal::route('/{record}/edit'),
        ];
    }
}