<?php

namespace App\Filament\Widgets;

use App\Models\RentInvoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OutstandingRentWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $outstanding = (float) RentInvoice::where('status', '!=', 'paid')->sum('balance');

        $chart = collect(range(5, 0))->map(function (int $i) {
            $date = now()->subMonths($i);

            return (float) RentInvoice::where('status', '!=', 'paid')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('balance');
        })->all();

        return [
            Stat::make('Outstanding Rent', 'KES '.number_format($outstanding))
                ->description('Unpaid rent balances')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger')
                ->chart($chart)
                ->chartColor('danger'),
        ];
    }
}
