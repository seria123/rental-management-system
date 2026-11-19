<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceRequest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendingMaintenanceWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Maintenance', MaintenanceRequest::where('status', 'pending')->count())
                ->description('Maintenance requests awaiting action')
                ->descriptionIcon('heroicon-m-wrench')
                ->color('warning'),
        ];
    }
}
