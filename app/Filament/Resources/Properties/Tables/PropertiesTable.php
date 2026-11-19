<?php

namespace App\Filament\Resources\Properties\Tables;

use App\Filament\Resources\Properties\Pages\ViewProperty;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PropertiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight('semibold'),
                TextColumn::make('property_owner.name')
                    ->label('Owner')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('property_type')
                    ->searchable(),
                TextColumn::make('city')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match (strtolower($state)) {
                        'active' => 'success',
                        'vacant' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('units.count')
                    ->label('Units')
                    ->counts('units')
                    ->alignCenter(),
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
                    ViewAction::make()->url(fn ($record) => ViewProperty::getUrl(['record' => $record])),
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
