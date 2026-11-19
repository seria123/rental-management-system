<?php

namespace App\Filament\Widgets;

use App\Models\Unit;
use Filament\Widgets\ChartWidget;

class OccupancyChartWidget extends ChartWidget
{
    protected ?string $heading = 'Property Occupancy';

    protected static ?int $sort = 30;

      protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $occupied = (int) Unit::where('status', 'occupied')->count();
        $vacant = (int) Unit::where('status', 'vacant')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Units',
                    'data' => [$occupied, $vacant],
                    'backgroundColor' => ['#10b981', '#f59e0b'],
                    'borderColor' => ['#059669', '#d97706'],
                    'borderWidth' => 2,
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Occupied', 'Vacant'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '70%',
            'plugins' => [
                'legend' => ['position' => 'bottom'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.label || "";
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return label + ": " + value + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
        ];
    }
}
