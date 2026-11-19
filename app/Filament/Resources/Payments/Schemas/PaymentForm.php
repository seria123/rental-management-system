<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required(),
                Select::make('lease_id')
                    ->relationship('lease', 'id'),
                TextInput::make('rent_invoice_id')
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                DatePicker::make('payment_date')
                    ->required(),
                TextInput::make('payment_method')
                    ->required(),
                TextInput::make('phone_number')
                    ->tel(),
                TextInput::make('transaction_reference'),
                TextInput::make('payment_type')
                    ->required()
                    ->default('rent'),
                TextInput::make('status')
                    ->required()
                    ->default('completed'),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
