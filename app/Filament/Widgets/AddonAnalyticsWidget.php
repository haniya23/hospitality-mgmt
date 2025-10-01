<?php

namespace App\Filament\Widgets;

use App\Models\SubscriptionAddon;
use App\Models\Subscription;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AddonAnalyticsWidget extends ChartWidget
{
    protected static ?string $heading = 'Add-on Analytics (Last 30 Days)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        // Get addon creation data for the last 30 days
        $addonData = Trend::model(SubscriptionAddon::class)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->sum('qty');

        // Get addon revenue data for the last 30 days
        $addonRevenueData = Trend::model(SubscriptionAddon::class)
            ->between(
                start: now()->subDays(30),
                end: now(),
            )
            ->perDay()
            ->sum('unit_price_cents');

        return [
            'datasets' => [
                [
                    'label' => 'Add-ons Sold',
                    'data' => $addonData->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(245, 158, 11)',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Add-on Revenue (₹)',
                    'data' => $addonRevenueData->map(fn (TrendValue $value) => $value->aggregate / 100), // Convert cents to rupees
                    'borderColor' => 'rgb(139, 69, 19)',
                    'backgroundColor' => 'rgba(139, 69, 19, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $addonData->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { 
                            if (value >= 1000) {
                                return "₹" + (value / 1000).toFixed(1) + "k";
                            }
                            return "₹" + value.toLocaleString();
                        }'
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { 
                            if (context.datasetIndex === 0) {
                                return "Add-ons: " + context.parsed.y;
                            } else {
                                return "Revenue: ₹" + context.parsed.y.toLocaleString();
                            }
                        }'
                    ],
                ],
            ],
        ];
    }
}
