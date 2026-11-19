<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTenantsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Tenant Registrations';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 30;

    public function getTable(): Table
    {
        return Table::make($this)
            ->query(Tenant::query()->latest('created_at')->limit(10))
            ->columns([
                TextColumn::make('name')
                    ->label('Tenant')
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('—'),
                TextColumn::make('unit.property.name')
                    ->label('Property')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtolower((string) $state)) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'info',
                    }),
                TextColumn::make('created_at')
                    ->label('Registered')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
