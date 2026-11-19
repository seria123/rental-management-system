<?php

namespace App\Filament\Resources\MoveOutRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MoveOutRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('unit_id')
                    ->relationship('unit', 'id')
                    ->required(),
                Select::make('lease_id')
                    ->relationship('lease', 'id')
                    ->required(),
                DatePicker::make('requested_move_out_date')
                    ->required(),
                DatePicker::make('actual_move_out_date'),
                TextInput::make('notice_days')
                    ->required()
                    ->numeric()
                    ->default(5),
                TextInput::make('reason'),
                TextInput::make('status')
                    ->required()
                    ->default('pending'),
                TextInput::make('deposit_amount')
                    ->required()
                    ->numeric(),
                TextInput::make('deposit_return_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('deposit_deduction_amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Textarea::make('damage_notes')
                    ->columnSpanFull(),
                TextInput::make('damage_cost')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                DateTimePicker::make('owner_approved_at'),
                Textarea::make('owner_response_notes')
                    ->columnSpanFull(),
                DateTimePicker::make('processed_at'),
            ]);
    }
}
