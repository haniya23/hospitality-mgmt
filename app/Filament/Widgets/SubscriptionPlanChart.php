<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanChart extends ChartWidget
{
    protected static ?string $heading = 'Subscription Plan Distribution';
    
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $planData = Subscription::select('plan_slug', DB::raw('count(*) as count'))
            ->groupBy('plan_slug')
            ->get();

        $labels = [];
        $data = [];
        $colors = [];

        foreach ($planData as $plan) {
            $labels[] = ucfirst($plan->plan_slug);
            $data[] = $plan->count;
            
            // Assign colors based on plan
            $colors[] = match ($plan->plan_slug) {
                'trial' => 'rgb(156, 163, 175)', // gray
                'starter' => 'rgb(59, 130, 246)', // blue
                'professional' => 'rgb(34, 197, 94)', // green
                default => 'rgb(156, 163, 175)',
            };
        }

        return [
            'datasets' => [
                [
                    'label' => 'Subscriptions',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
