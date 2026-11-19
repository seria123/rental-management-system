<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MonthlyRentCollectedWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $collected = (float) Payment::where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum('amount');

        $chart = collect(range(5, 0))->map(function (int $i) {
            $date = now()->subMonths($i);

            return (float) Payment::where('status', 'completed')
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');
        })->all();

        $previous = $chart[count($chart) - 2] ?? 0;
        $change = $previous > 0 ? round((($collected - $previous) / $previous) * 100, 1) : null;

        return [
            Stat::make('Rent Collected This Month', 'KES '.number_format($collected))
                ->description($change === null
                    ? 'Rent collected this month'
                    : ($change >= 0 ? "▲ {$change}% vs last month" : '▼ '.abs($change).'% vs last month'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->chart($chart)
                ->chartColor('success'),
        ];
    }
}
