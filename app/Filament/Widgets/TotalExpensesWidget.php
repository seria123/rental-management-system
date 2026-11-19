<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalExpensesWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $total = (float) Expense::sum('amount');

        $chart = collect(range(5, 0))->map(function (int $i) {
            $date = now()->subMonths($i);

            return (float) Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');
        })->all();

        return [
            Stat::make('Total Expenses', 'KES '.number_format($total))
                ->description('All recorded expenses')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color('warning')
                ->chart($chart)
                ->chartColor('warning'),
        ];
    }
}
