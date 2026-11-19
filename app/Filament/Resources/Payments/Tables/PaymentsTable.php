<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Filament\Resources\Payments\Pages\ViewPayment;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->weight('semibold')
                    ->placeholder('—'),
                TextColumn::make('lease.unit.unit_number')
                    ->label('Unit')
                    ->placeholder('—'),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('KES')
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('transaction_reference')
                    ->label('M-Pesa Reference')
                    ->searchable()
                    ->placeholder('—')
                    ->copyable(),
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
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'completed' => 'Completed',
                        'pending' => 'Pending',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->url(fn ($record) => ViewPayment::getUrl(['record' => $record])),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
