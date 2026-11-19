<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\Properties\PropertyResource;
use App\Filament\Resources\Tenants\TenantResource;

use App\Filament\Widgets\IncomeVsExpensesChartWidget;
use App\Filament\Widgets\MonthlyRentCollectedWidget;
use App\Filament\Widgets\OccupancyChartWidget;
use App\Filament\Widgets\OccupiedUnitsWidget;
use App\Filament\Widgets\OutstandingRentWidget;
use App\Filament\Widgets\PendingMaintenanceTableWidget;
use App\Filament\Widgets\RecentActivityWidget;
use App\Filament\Widgets\RecentTransactionsWidget;
use App\Filament\Widgets\TotalExpensesWidget;
use App\Filament\Widgets\TotalPropertiesWidget;
use App\Filament\Widgets\TotalTenantsWidget;
use App\Filament\Widgets\TotalUnitsWidget;
use App\Filament\Widgets\VacantUnitsWidget;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static \UnitEnum|string|null $navigationGroup = 'Overview';

    protected static ?int $navigationSort = 1;

    /**
     * Dashboard heading.
     */
    public function getHeading(): string
    {
        $first = auth()->user()?->name
            ? str(auth()->user()->name)->before(' ')
            : 'Administrator';

        return "Welcome back, {$first}";
    }

    /**
     * Dashboard description.
     */
    public function getSubheading(): ?string
    {
        return 'Manage your properties, tenants, finances and daily operations. '
            .now()->format('l, j F Y');
    }

    /**
     * Date filters.
     */
    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                DatePicker::make('from_date')
                    ->label('Start date')
                    ->placeholder('Select start date')
                    ->native(false),

                DatePicker::make('to_date')
                    ->label('End date')
                    ->placeholder('Select end date')
                    ->native(false),
            ])
            ->columns([
                'default' => 1,
                'md' => 2,
            ]);
    }

    /**
     * Quick action buttons.
     */
    public function getHeaderActions(): array
    {
        return [
            Action::make('addProperty')
                ->label('Add Property')
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->url(PropertyResource::getUrl('create'))
                ->color('primary'),

            Action::make('registerTenant')
                ->label('Register Tenant')
                ->icon(Heroicon::OutlinedUserPlus)
                ->url(TenantResource::getUrl('create'))
                ->color('gray'),

            Action::make('recordPayment')
                ->label('Record Payment')
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->url(PaymentResource::getUrl('create'))
                ->color('success'),
        ];
    }

    /**
     * Responsive dashboard grid.
     */
    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'sm' => 2,
            'lg' => 3,
            'xl' => 4,
        ];
    }

    /**
     * Dashboard widgets.
     */
    public function getWidgets(): array
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | PROPERTY PERFORMANCE
            |--------------------------------------------------------------------------
            */

            TotalPropertiesWidget::class,
            TotalUnitsWidget::class,
            OccupiedUnitsWidget::class,
            VacantUnitsWidget::class,

            /*
            |--------------------------------------------------------------------------
            | FINANCIAL OVERVIEW
            |--------------------------------------------------------------------------
            */

            MonthlyRentCollectedWidget::class,
            OutstandingRentWidget::class,
            TotalExpensesWidget::class,
            TotalTenantsWidget::class,

            /*
            |--------------------------------------------------------------------------
            | ANALYTICS
            |--------------------------------------------------------------------------
            */

            IncomeVsExpensesChartWidget::class,
            OccupancyChartWidget::class,

            /*
            |--------------------------------------------------------------------------
            | PROPERTY OPERATIONS
            |--------------------------------------------------------------------------
            */

            RecentTransactionsWidget::class,
            PendingMaintenanceTableWidget::class,

            /*
            |--------------------------------------------------------------------------
            | SYSTEM ACTIVITY
            |--------------------------------------------------------------------------
            */

            RecentActivityWidget::class,
        ];
    }
}