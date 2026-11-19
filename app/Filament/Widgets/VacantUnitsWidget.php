<?php

namespace App\Filament\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VacantUnitsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Vacant Units', Unit::where('status', 'vacant')->count())
                ->description('Units available for rent')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('warning'),
        ];
    }
}
