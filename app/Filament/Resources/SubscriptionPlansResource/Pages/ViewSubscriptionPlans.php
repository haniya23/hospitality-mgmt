<?php

namespace App\Filament\Resources\SubscriptionPlansResource\Pages;

use App\Filament\Resources\SubscriptionPlansResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscriptionPlans extends ViewRecord
{
    protected static string $resource = SubscriptionPlansResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('renew_subscription')
                ->label('Renew Subscription')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('renewal_period')
                        ->label('Renewal Period')
                        ->options([
                            '1' => '1 Month',
                            '3' => '3 Months',
                            '6' => '6 Months',
                            '12' => '12 Months',
                        ])
                        ->default('1')
                        ->required(),
                    \Filament\Forms\Components\Toggle::make('auto_charge')
                        ->label('Auto-charge existing payment method')
                        ->default(false),
                ])
                ->action(function (array $data) {
                    $months = (int) $data['renewal_period'];
                    $this->record->update([
                        'current_period_end' => $this->record->current_period_end->addMonths($months),
                        'status' => 'active',
                    ]);
                    
                    // Log renewal
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties(['renewal_months' => $months])
                        ->log('Subscription renewed');
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->status, ['active', 'expired'])),

            Actions\Action::make('add_addon')
                ->label('Add Add-on')
                ->icon('heroicon-o-plus-circle')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\TextInput::make('qty')
                        ->label('Quantity')
                        ->numeric()
                        ->required()
                        ->minValue(1)
                        ->default(1),
                    \Filament\Forms\Components\TextInput::make('unit_price_cents')
                        ->label('Unit Price (cents)')
                        ->numeric()
                        ->required()
                        ->default(19900) // ₹199
                        ->helperText('Default: ₹199 per accommodation'),
                    \Filament\Forms\Components\DateTimePicker::make('cycle_end')
                        ->label('Add-on Valid Until')
                        ->default($this->record->current_period_end)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->addons()->create([
                        'qty' => $data['qty'],
                        'unit_price_cents' => $data['unit_price_cents'],
                        'cycle_start' => now(),
                        'cycle_end' => $data['cycle_end'],
                    ]);
                    
                    $this->record->increment('addon_count', $data['qty']);
                    
                    // Log addon addition
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties($data)
                        ->log('Add-on added to subscription');
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => $this->record->status === 'active'),

            Actions\Action::make('upgrade_plan')
                ->label('Upgrade Plan')
                ->icon('heroicon-o-arrow-up')
                ->color('warning')
                ->form([
                    \Filament\Forms\Components\Select::make('new_plan_slug')
                        ->label('Upgrade To')
                        ->options(function () {
                            $currentPlan = $this->record->plan_slug;
                            $options = [];
                            
                            if ($currentPlan === 'trial') {
                                $options['starter'] = 'Starter Plan (₹999/month)';
                                $options['professional'] = 'Professional Plan (₹1999/month)';
                            } elseif ($currentPlan === 'starter') {
                                $options['professional'] = 'Professional Plan (₹1999/month)';
                            }
                            
                            return $options;
                        })
                        ->required()
                        ->visible(fn () => in_array($this->record->plan_slug, ['trial', 'starter'])),
                ])
                ->action(function (array $data) {
                    $planDetails = match($data['new_plan_slug']) {
                        'starter' => [
                            'plan_slug' => 'starter',
                            'plan_name' => 'Starter Plan',
                            'base_accommodation_limit' => 5,
                            'price_cents' => 99900,
                        ],
                        'professional' => [
                            'plan_slug' => 'professional',
                            'plan_name' => 'Professional Plan',
                            'base_accommodation_limit' => 15,
                            'price_cents' => 199900,
                        ],
                        default => [],
                    };
                    
                    $this->record->update($planDetails);
                    
                    // Log upgrade
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties(['upgraded_to' => $data['new_plan_slug']])
                        ->log('Subscription plan upgraded');
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->requiresConfirmation()
                ->visible(fn () => in_array($this->record->plan_slug, ['trial', 'starter']) && $this->record->status === 'active'),

            Actions\Action::make('cancel_subscription')
                ->label('Cancel Subscription')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('cancellation_reason')
                        ->label('Cancellation Reason')
                        ->required()
                        ->maxLength(500),
                    \Filament\Forms\Components\Toggle::make('immediate_cancellation')
                        ->label('Cancel immediately (otherwise cancel at period end)')
                        ->default(false),
                ])
                ->action(function (array $data) {
                    $updateData = ['status' => 'cancelled'];
                    
                    if ($data['immediate_cancellation']) {
                        $updateData['current_period_end'] = now();
                    }
                    
                    $this->record->update($updateData);
                    
                    // Log cancellation
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->withProperties($data)
                        ->log('Subscription cancelled');
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'active'),

            Actions\Action::make('reactivate')
                ->label('Reactivate Subscription')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update([
                        'status' => 'active',
                        'current_period_end' => now()->addMonth(), // Extend by one month
                    ]);
                    
                    // Log reactivation
                    activity()
                        ->performedOn($this->record)
                        ->causedBy(auth()->user())
                        ->log('Subscription reactivated');
                    
                    $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
                })
                ->visible(fn () => in_array($this->record->status, ['cancelled', 'expired'])),

            Actions\Action::make('view_user')
                ->label('View User Profile')
                ->icon('heroicon-o-user')
                ->url(fn () => '#') // TODO: Link to UserResource when available
                ->color('gray'),

            Actions\Action::make('view_payments')
                ->label('View Payment History')
                ->icon('heroicon-o-credit-card')
                ->url(fn () => '#') // TODO: Link to PaymentResource when available
                ->color('info'),

            Actions\Action::make('generate_invoice')
                ->label('Generate Invoice')
                ->icon('heroicon-o-document-text')
                ->color('secondary')
                ->action(function () {
                    // Invoice generation logic here
                    $this->notify('success', 'Invoice generated successfully!');
                }),
        ];
    }
}
