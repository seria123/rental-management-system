<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ticket_number')
                    ->required(),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('category_id')
                    ->relationship('category', 'name'),
                TextInput::make('subject')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Select::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'critical' => 'Critical'])
                    ->default('medium')
                    ->required(),
                Select::make('status')
                    ->options(['open' => 'Open', 'pending' => 'Pending', 'resolved' => 'Resolved', 'closed' => 'Closed'])
                    ->default('open')
                    ->required(),
                TextInput::make('assigned_to')
                    ->numeric(),
                DateTimePicker::make('resolved_at'),
            ]);
    }
}
