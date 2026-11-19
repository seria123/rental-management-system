<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class IncomeVsExpensesChartWidget extends ChartWidget
{
    protected ?string $heading = 'Income vs Expenses (last 12 months)';

    protected static ?int $sort = 10;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $labels = [];
        $income = [];
        $expenses = [];

        foreach (range(11, 0) as $i) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $labels[] = $date->format('M Y');

            $income[] = (float) Payment::where('status', 'completed')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');

            $expenses[] = (float) Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Income (KES)',
                    'data' => $income,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.75)',
                    'borderColor' => '#10b981',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Expenses (KES)',
                    'data' => $expenses,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'borderColor' => '#ef4444',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
            'plugins' => [
                'legend' => ['position' => 'bottom'],
            ],
        ];
    }
}
