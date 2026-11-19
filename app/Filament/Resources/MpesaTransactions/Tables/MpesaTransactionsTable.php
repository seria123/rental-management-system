<?php

namespace App\Filament\Resources\MpesaTransactions\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MpesaTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('transaction_id')
                    ->label('M-Pesa Receipt')
                    ->searchable()
                    ->copyable()
                    ->placeholder('—')
                    ->weight('medium'),
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('amount')
                    ->money('KES')
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('transaction_type')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'pending' => 'warning',
                        'failed', 'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('result_desc')
                    ->label('Result')
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Initiated')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}
