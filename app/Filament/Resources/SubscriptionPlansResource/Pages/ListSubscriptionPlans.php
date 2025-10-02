<?php

namespace App\Filament\Resources\SubscriptionPlansResource\Pages;

use App\Filament\Resources\SubscriptionPlansResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSubscriptionPlans extends ListRecords
{
    protected static string $resource = SubscriptionPlansResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('view_trial_users')
                ->label('View Trial Users')
                ->icon('heroicon-o-gift')
                ->color('warning')
                ->action(function () {
                    $trialUsers = \App\Models\User::where('subscription_status', 'trial')
                                                 ->where('is_trial_active', true)
                                                 ->get();
                    
                    $message = "Found {$trialUsers->count()} active trial users:\n\n";
                    foreach ($trialUsers as $user) {
                        $message .= "• {$user->name} ({$user->mobile_number}) - Expires: {$user->trial_ends_at->format('M d, Y')}\n";
                    }
                    
                    $this->notify('info', $message);
                })
                ->tooltip('View trial users from User model'),

            Actions\Action::make('subscription_analytics')
                ->label('View Analytics')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->url(static::getResource()::getUrl('index') . '?view=analytics')
                ->tooltip('View subscription analytics and reports'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Subscriptions'),
            
            'trial' => Tab::make('Trial Plans')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('plan_slug', 'trial'))
                ->badge(fn () => 
                    $this->getModel()::where('plan_slug', 'trial')->count() + 
                    \App\Models\User::where('subscription_status', 'trial')->where('is_trial_active', true)->count()
                )
                ->icon('heroicon-o-gift'),
            
            'starter' => Tab::make('Starter Plans')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('plan_slug', 'starter'))
                ->badge(fn () => $this->getModel()::where('plan_slug', 'starter')->count())
                ->icon('heroicon-o-rocket-launch'),
            
            'professional' => Tab::make('Professional Plans')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('plan_slug', 'professional'))
                ->badge(fn () => $this->getModel()::where('plan_slug', 'professional')->count())
                ->icon('heroicon-o-star'),
            
            'starter_with_addons' => Tab::make('Starter + Add-ons')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('plan_slug', 'starter')
                          ->where('addon_count', '>', 0)
                )
                ->badge(fn () => 
                    $this->getModel()::where('plan_slug', 'starter')
                                   ->where('addon_count', '>', 0)
                                   ->count()
                )
                ->icon('heroicon-o-plus-circle'),
            
            'professional_with_addons' => Tab::make('Professional + Add-ons')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('plan_slug', 'professional')
                          ->where('addon_count', '>', 0)
                )
                ->badge(fn () => 
                    $this->getModel()::where('plan_slug', 'professional')
                                   ->where('addon_count', '>', 0)
                                   ->count()
                )
                ->icon('heroicon-o-star'),
            
            'active' => Tab::make('Active')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'active'))
                ->badge(fn () => $this->getModel()::where('status', 'active')->count())
                ->icon('heroicon-o-check-circle'),
            
            'expiring_soon' => Tab::make('Expiring Soon')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('current_period_end', '<=', now()->addDays(30))
                          ->where('current_period_end', '>', now())
                          ->where('status', 'active')
                )
                ->badge(fn () => 
                    $this->getModel()::where('current_period_end', '<=', now()->addDays(30))
                                   ->where('current_period_end', '>', now())
                                   ->where('status', 'active')
                                   ->count()
                )
                ->icon('heroicon-o-exclamation-triangle'),
            
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('current_period_end', '<', now())
                )
                ->badge(fn () => 
                    $this->getModel()::where('current_period_end', '<', now())->count()
                )
                ->icon('heroicon-o-x-circle'),
        ];
    }
}
