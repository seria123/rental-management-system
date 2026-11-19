<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\RentInvoice;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;

class FinancialReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentChartBar;

    protected static \UnitEnum|string|null $navigationGroup = 'Financial Management';

    protected static ?string $navigationLabel = 'Financial Reports';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Financial Reports';

    protected string $view = 'filament.pages.financial-reports';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->canModule('finance');
    }

    public function getViewData(): array
    {
        $months = collect(range(11, 0))->map(function (int $i) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);

            $income = (float) Payment::where('status', 'completed')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('amount');

            $expense = (float) Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');

            return [
                'label' => $date->format('M Y'),
                'income' => $income,
                'expense' => $expense,
                'net' => $income - $expense,
            ];
        });

        $totalIncome = $months->sum('income');
        $totalExpense = $months->sum('expense');
        $outstanding = (float) RentInvoice::where('status', '!=', 'paid')->sum('balance');

        $byCategory = Expense::query()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return [
            'months' => $months,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'net' => $totalIncome - $totalExpense,
            'outstanding' => $outstanding,
            'byCategory' => $byCategory,
            'maxValue' => max([$months->max('income') ?: 0, $months->max('expense') ?: 0, 1]),
        ];
    }
}
