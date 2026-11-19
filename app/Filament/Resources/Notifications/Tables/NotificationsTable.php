<?php

namespace App\Filament\Resources\Notifications\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class NotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_read')
                    ->label('Read')
                    ->boolean(),
                TextColumn::make('title')
                    ->searchable()
                    ->weight('semibold')
                    ->limit(40),
                TextColumn::make('message')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'warning' => 'warning',
                        'error', 'danger' => 'danger',
                        default => 'info',
                    }),
                TextColumn::make('user.name')
                    ->label('User')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('channel')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_read')->label('Read status'),
                SelectFilter::make('type')
                    ->options([
                        'info' => 'Info',
                        'success' => 'Success',
                        'warning' => 'Warning',
                        'error' => 'Error',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('markAsRead')
                        ->label('Mark as read')
                        ->icon('heroicon-o-check')
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => true]))
                        ->deselectRecordsAfterCompletion(),
                    BulkAction::make('markAsUnread')
                        ->label('Mark as unread')
                        ->icon('heroicon-o-envelope')
                        ->action(fn (Collection $records) => $records->each->update(['is_read' => false]))
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
