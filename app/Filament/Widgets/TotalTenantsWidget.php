<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalTenantsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Tenants', Tenant::count())
                ->description('Registered tenants')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
