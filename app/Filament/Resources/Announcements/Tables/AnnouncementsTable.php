<?php

namespace App\Filament\Resources\Announcements\Tables;

use App\Filament\Resources\Announcements\Pages\ViewAnnouncement;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('target_type')
                    ->searchable(),
                TextColumn::make('property.name')
                    ->searchable(),
                TextColumn::make('unit.id')
                    ->searchable(),
                TextColumn::make('tenant_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('is_pinned')
                    ->boolean(),
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
                    ViewAction::make()->url(fn ($record) => ViewAnnouncement::getUrl(['record' => $record])),
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
