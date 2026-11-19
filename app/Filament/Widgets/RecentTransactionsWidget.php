<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Rent Payments';

     protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected static ?int $sort = 20;

    public function getTable(): Table
    {
        return Table::make($this)
            ->query(Payment::with(['tenant', 'lease.unit.property'])->latest('payment_date')->limit(10))
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('lease.unit.unit_number')
                    ->label('Unit')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('KES')
                    ->weight('semibold'),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'completed', 'paid' => 'success',
                        'pending' => 'warning',
                        'failed', 'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('payment_date')
                    ->label('Date')
                    ->date('M j, Y')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
