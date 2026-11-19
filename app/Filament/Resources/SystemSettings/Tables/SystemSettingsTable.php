<?php

namespace App\Filament\Resources\SystemSettings\Tables;

use App\Models\SystemSetting;
use App\Filament\Resources\SystemSettings\Pages\ViewSystemSetting;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SystemSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('group')
            ->columns([
                TextColumn::make('key')
                    ->searchable()
                    ->weight('medium')
                    ->copyable(),
                TextColumn::make('group')
                    ->badge()
                    ->sortable(),
                TextColumn::make('value')
                    ->limit(40)
                    ->formatStateUsing(fn ($state, $record) => $record?->is_encrypted && filled($state) ? '••••••••' : (string) $state),
                TextColumn::make('input_type')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_encrypted')
                    ->label('Encrypted')
                    ->boolean(),
                TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options(fn () => SystemSetting::query()
                        ->distinct()
                        ->pluck('group', 'group')
                        ->toArray()),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->url(fn ($record) => ViewSystemSetting::getUrl(['record' => $record])),
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
