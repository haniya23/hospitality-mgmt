<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\PropertyResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('toggle_admin')
                ->label(fn () => $this->record->is_admin ? 'Remove Admin Access' : 'Grant Admin Access')
                ->icon(fn () => $this->record->is_admin ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                ->color(fn () => $this->record->is_admin ? 'warning' : 'danger')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['is_admin' => !$this->record->is_admin]);
                    
                    // Log admin status change
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties(['new_admin_status' => !$this->record->is_admin])
                        ->log('Admin access ' . ($this->record->is_admin ? 'granted' : 'removed'));
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\Action::make('toggle_active')
                ->label(fn () => $this->record->is_active ? 'Deactivate User' : 'Activate User')
                ->icon(fn () => $this->record->is_active ? 'heroicon-o-pause' : 'heroicon-o-play')
                ->color(fn () => $this->record->is_active ? 'warning' : 'success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['is_active' => !$this->record->is_active]);
                    
                    // Log activation status change
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties(['new_active_status' => !$this->record->is_active])
                        ->log('User ' . ($this->record->is_active ? 'activated' : 'deactivated'));
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                }),

            Actions\Action::make('upgrade_plan')
                ->label('Upgrade Plan')
                ->icon('heroicon-o-arrow-up')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('new_plan')
                        ->label('New Plan')
                        ->options([
                            'starter' => 'Starter Plan',
                            'professional' => 'Professional Plan',
                        ])
                        ->required()
                        ->visible(fn () => $this->record->subscription_status === 'trial'),
                    
                    \Filament\Forms\Components\Select::make('billing_cycle')
                        ->label('Billing Cycle')
                        ->options([
                            'monthly' => 'Monthly',
                            'yearly' => 'Yearly',
                        ])
                        ->default('yearly'),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'subscription_status' => $data['new_plan'],
                        'is_trial_active' => false,
                        'subscription_ends_at' => now()->addYear(),
                        'properties_limit' => $data['new_plan'] === 'professional' ? 5 : 1,
                        'billing_cycle' => $data['billing_cycle'],
                    ]);
                    
                    // Log plan upgrade
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties(['upgraded_to' => $data['new_plan']])
                        ->log('User plan upgraded');
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->subscription_status === 'trial'),

            Actions\Action::make('extend_trial')
                ->label('Extend Trial')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('extension_days')
                        ->label('Extension Days')
                        ->options([
                            '7' => '7 days',
                            '15' => '15 days',
                            '30' => '30 days',
                            '60' => '60 days',
                        ])
                        ->default('15'),
                    
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->label('Reason for Extension')
                        ->maxLength(500)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'trial_ends_at' => $this->record->trial_ends_at->addDays((int) $data['extension_days']),
                    ]);
                    
                    // Log trial extension
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties([
                            'extension_days' => $data['extension_days'],
                            'reason' => $data['reason']
                        ])
                        ->log('Trial extended');
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->subscription_status === 'trial' && $this->record->is_trial_active),

            Actions\Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('secondary')
                ->form([
                    \Filament\Forms\Components\TextInput::make('new_pin')
                        ->label('New PIN')
                        ->password()
                        ->required()
                        ->minLength(4)
                        ->maxLength(6),
                    \Filament\Forms\Components\TextInput::make('confirm_pin')
                        ->label('Confirm PIN')
                        ->password()
                        ->required()
                        ->same('new_pin'),
                ])
                ->action(function (array $data) {
                    $this->record->update([
                        'pin_hash' => \Illuminate\Support\Facades\Hash::make($data['new_pin']),
                    ]);
                    
                    // Log password reset
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->log('User PIN reset');
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->requiresConfirmation(),

            Actions\Action::make('view_properties')
                ->label('View Properties')
                ->icon('heroicon-o-building-office')
                ->url(fn () => PropertyResource::getUrl('index', ['tableFilters[owner][value]' => $this->record->id]))
                ->color('gray'),

            Actions\Action::make('view_bookings')
                ->label('View Bookings')
                ->icon('heroicon-o-calendar-days')
                ->url(fn () => \App\Filament\Resources\ReservationResource::getUrl('index', ['tableFilters[guest][value]' => $this->record->id]))
                ->color('info'),

            Actions\Action::make('impersonate')
                ->label('Impersonate User')
                ->icon('heroicon-o-user-circle')
                ->color('secondary')
                ->requiresConfirmation()
                ->modalDescription('This will log you in as this user. Are you sure?')
                ->action(function () {
                    // TODO: Implement user impersonation
                    $this->notify('info', 'Impersonation feature not yet implemented');
                })
                ->visible(fn () => auth()->user()->is_admin && !$this->record->is_admin),
        ];
    }
}
