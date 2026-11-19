<?php

namespace App\Filament\Resources\Expenses\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('property.name')
                    ->label('Property')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable()
                    ->weight('semibold'),
                TextColumn::make('category')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('amount')
                    ->money('KES')
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('expense_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
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
