<?php

namespace App\Filament\Resources\MpesaTransactions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MpesaTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->label('Tenant')
                    ->searchable()
                    ->preload(),
                TextInput::make('transaction_id')->label('M-Pesa Receipt')->maxLength(255),
                TextInput::make('amount')->numeric()->prefix('KES'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
                Select::make('transaction_type')
                    ->options([
                        'paybill' => 'Paybill',
                        'till' => 'Till',
                        'stk' => 'STK Push',
                    ]),
                TextInput::make('checkout_request_id')->disabled(),
                TextInput::make('merchant_request_id')->disabled(),
                TextInput::make('result_code')->disabled(),
                TextInput::make('result_desc')->disabled(),
                Textarea::make('payload')
                    ->disabled()
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state),
            ]);
    }
}
