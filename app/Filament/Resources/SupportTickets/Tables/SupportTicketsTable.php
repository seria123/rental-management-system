<?php

namespace App\Filament\Resources\SupportTickets\Tables;

use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupportTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Ticket')
                    ->searchable()
                    ->weight('semibold'),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('subject')
                    ->searchable(),
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
                        'open' => 'warning',
                        'in_progress' => 'info',
                        'resolved', 'closed' => 'success',
                        default => 'gray',
                    }),
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
                    ViewAction::make()->url(fn ($record) => ViewSupportTicket::getUrl(['record' => $record])),
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
