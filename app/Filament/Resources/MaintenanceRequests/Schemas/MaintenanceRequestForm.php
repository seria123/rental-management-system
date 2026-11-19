<?php

namespace App\Filament\Resources\MaintenanceRequests\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MaintenanceRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('property_id')
                    ->relationship('property', 'name')
                    ->required(),
                Select::make('unit_id')
                    ->relationship('unit', 'id')
                    ->required(),
                Select::make('tenant_id')
                    ->relationship('tenant', 'name'),
                TextInput::make('title')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('priority')
                    ->required()
                    ->default('medium'),
                TextInput::make('status')
                    ->required()
                    ->default('open'),
                DateTimePicker::make('reported_at')
                    ->required(),
                DateTimePicker::make('resolved_at'),
            ]);
    }
}
