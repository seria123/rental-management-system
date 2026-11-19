<?php

namespace App\Filament\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OccupiedUnitsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Occupied Units', Unit::where('status', 'occupied')->count())
                ->description('Units with active leases')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
