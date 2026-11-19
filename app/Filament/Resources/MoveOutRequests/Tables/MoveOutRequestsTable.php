<?php

namespace App\Filament\Resources\MoveOutRequests\Tables;

use App\Filament\Resources\MoveOutRequests\Pages\ViewMoveOutRequest;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MoveOutRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('unit.id')
                    ->searchable(),
                TextColumn::make('lease.id')
                    ->searchable(),
                TextColumn::make('requested_move_out_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('actual_move_out_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('notice_days')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reason')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'pending', 'requested' => 'warning',
                        'approved' => 'info',
                        'processing' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('deposit_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deposit_return_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('deposit_deduction_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('damage_cost')
                    ->money()
                    ->sortable(),
                TextColumn::make('owner_approved_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->url(fn ($record) => ViewMoveOutRequest::getUrl(['record' => $record])),
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
