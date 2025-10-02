<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('user_statistics')
                ->label('View Statistics')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->action(function () {
                    $stats = [
                        'Total Users' => User::count(),
                        'Active Users' => User::where('is_active', true)->count(),
                        'Admin Users' => User::where('is_admin', true)->count(),
                        'Trial Users' => User::where('subscription_status', 'trial')->where('is_trial_active', true)->count(),
                        'Starter Users' => User::where('subscription_status', 'starter')->count(),
                        'Professional Users' => User::where('subscription_status', 'professional')->count(),
                        'Expiring Trials (7 days)' => User::where('trial_ends_at', '<=', now()->addDays(7))
                                                         ->where('trial_ends_at', '>', now())
                                                         ->where('is_trial_active', true)->count(),
                        'Expiring Subscriptions (30 days)' => User::where('subscription_ends_at', '<=', now()->addDays(30))
                                                                 ->where('subscription_ends_at', '>', now())->count(),
                    ];
                    
                    $message = "User Statistics:\n\n";
                    foreach ($stats as $label => $count) {
                        $message .= "• {$label}: {$count}\n";
                    }
                    
                    $this->notify('info', $message);
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All Users')
                ->badge(fn () => User::count()),
            
            'active' => Tab::make('Active Users')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true))
                ->badge(fn () => User::where('is_active', true)->count())
                ->icon('heroicon-o-check-circle'),
            
            'admins' => Tab::make('Admin Users')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_admin', true))
                ->badge(fn () => User::where('is_admin', true)->count())
                ->icon('heroicon-o-shield-check'),
            
            'trial' => Tab::make('Trial Users')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('subscription_status', 'trial')
                          ->where('is_trial_active', true)
                )
                ->badge(fn () => 
                    User::where('subscription_status', 'trial')
                        ->where('is_trial_active', true)->count()
                )
                ->icon('heroicon-o-gift'),
            
            'starter' => Tab::make('Starter Users')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('subscription_status', 'starter'))
                ->badge(fn () => User::where('subscription_status', 'starter')->count())
                ->icon('heroicon-o-rocket-launch'),
            
            'professional' => Tab::make('Professional Users')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('subscription_status', 'professional'))
                ->badge(fn () => User::where('subscription_status', 'professional')->count())
                ->icon('heroicon-o-star'),
            
            'expiring_trials' => Tab::make('Expiring Trials')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('trial_ends_at', '<=', now()->addDays(7))
                          ->where('trial_ends_at', '>', now())
                          ->where('is_trial_active', true)
                )
                ->badge(fn () => 
                    User::where('trial_ends_at', '<=', now()->addDays(7))
                        ->where('trial_ends_at', '>', now())
                        ->where('is_trial_active', true)->count()
                )
                ->icon('heroicon-o-exclamation-triangle'),
            
            'expiring_subscriptions' => Tab::make('Expiring Subscriptions')
                ->modifyQueryUsing(fn (Builder $query) => 
                    $query->where('subscription_ends_at', '<=', now()->addDays(30))
                          ->where('subscription_ends_at', '>', now())
                )
                ->badge(fn () => 
                    User::where('subscription_ends_at', '<=', now()->addDays(30))
                        ->where('subscription_ends_at', '>', now())->count()
                )
                ->icon('heroicon-o-clock'),
        ];
    }
}
