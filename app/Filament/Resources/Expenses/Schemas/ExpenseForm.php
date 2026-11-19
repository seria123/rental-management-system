<?php

namespace App\Filament\Resources\Expenses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('property_id')
                    ->required()
                    ->numeric(),
                TextInput::make('title'),
                Select::make('unit_id')
                    ->relationship('unit', 'id'),
                TextInput::make('description')
                    ->required(),
                TextInput::make('category')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('expense_date')
                    ->required(),
                TextInput::make('payment_method'),
                TextInput::make('phone_number')
                    ->tel(),
                TextInput::make('reference'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
