<?php

namespace App\Filament\Resources\MaintenanceRequests\Tables;

use App\Filament\Resources\MaintenanceRequests\Pages\ViewMaintenanceRequest;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MaintenanceRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->weight('semibold'),
                TextColumn::make('property.name')
                    ->label('Property')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('unit.unit_number')
                    ->label('Unit')
                    ->placeholder('—'),
                TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'high', 'urgent' => 'danger',
                        'medium' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'pending' => 'warning',
                        'in_progress', 'assigned' => 'info',
                        'resolved', 'completed' => 'success',
                        'closed' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('reported_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->url(fn ($record) => ViewMaintenanceRequest::getUrl(['record' => $record])),
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
