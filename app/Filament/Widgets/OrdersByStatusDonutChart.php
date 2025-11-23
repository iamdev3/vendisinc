<?php

namespace App\Filament\Widgets;

use App\Enums\OrderStatus;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersByStatusDonutChart extends ChartWidget
{
    protected ?string $heading      = 'Orders Counts by Status';
    protected ?string $description  = 'Distribution of orders across different statuses with count value';
    protected string $color         = 'success';
    protected static ?int $sort     = 2;
    protected ?string $maxHeight    = "450px";

    protected function getData(): array
    {
        // Get count of orders for each status
        $orderCounts = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Define all possible statuses with default values based on OrderStatus enum
        $statuses = [
            'pending' => 0,
            'confirmed' => 0,
            'processing' => 0,
            'shipped' => 0,
            'delivered' => 0,
            'cancelled' => 0,
            'refunded' => 0,
            'on_hold' => 0,
        ];

        // Merge with actual data
        $statuses = array_merge($statuses, $orderCounts);

        // Define colors for each status based on OrderStatus enum
        $colors = [
            'pending'    => '#f59e0b',   // amber (warning)
            'confirmed'  => '#3b82f6',   // blue (info)
            'processing' => '#8b5cf6',   // violet (primary)
            'shipped'    => '#0ea5e9',   // sky (success)
            'delivered'  => '#10b981',   // emerald (success)
            'cancelled'  => '#ef4444',   // red (danger)
            'refunded'   => '#64748b',   // slate (gray)
            'on_hold'    => '#f97316',   // orange (warning)
        ];

        return [
            'datasets' => [
                [
                    'label'             => 'Orders',
                    'data'              => array_values($statuses),
                    'backgroundColor'   => array_values($colors),
                    'borderColor'       => '#ffffff',
                    'borderWidth'       => 2,
                    'cutout'            => '50%', // This makes it a donut chart
                    'datalabels' => [
                        'color' => '#fff',
                        'font' => [
                            'weight' => 'bold'
                        ],
                        'formatter' => function($value) {
                            return $value > 0 ? $value : '';
                        }
                    ]
                ],
            ],
            'labels' => array_keys($statuses),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'datalabels' => [
                    'color' => '#fff',
                    'font' => [
                        'weight' => 'bold'
                    ],
                    'formatter' => function($value) {
                        return $value > 0 ? $value : '';
                    }
                ]
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
        ];
    }
}