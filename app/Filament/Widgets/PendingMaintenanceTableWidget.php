<?php

namespace App\Filament\Widgets;

use App\Models\MaintenanceRequest;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingMaintenanceTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Pending Maintenance Requests';

     protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected static ?int $sort = 40;

    public function getTable(): Table
    {
        return Table::make($this)
            ->query(MaintenanceRequest::query()->where('status', 'pending')->latest('created_at')->limit(10))
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Issue')
                    ->searchable()
                    ->limit(40)
                    ->weight('medium'),
                TextColumn::make('property.name')
                    ->label('Property')
                    ->placeholder('—'),
                TextColumn::make('unit.unit_number')
                    ->label('Unit')
                    ->placeholder('—'),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtolower((string) $state)) {
                        'high', 'urgent' => 'danger',
                        'medium' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Reported')
                    ->since(),
            ])
            ->paginated(false);
    }
}
