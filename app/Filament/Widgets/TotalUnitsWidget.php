<?php

namespace App\Filament\Widgets;

use App\Models\Unit;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TotalUnitsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Units', Unit::count())
                ->description('All rental units')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('primary'),
        ];
    }
}
