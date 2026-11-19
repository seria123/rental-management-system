<?php

namespace App\Filament\Widgets;

use App\Models\UserActivityLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentActivityWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent System Activities';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 50;

    public function getTable(): Table
    {
        return Table::make($this)
            ->query(UserActivityLog::with('user')->latest('created_at')->limit(10))
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Event')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Description')
                    ->limit(70)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Time')
                    ->since()
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
