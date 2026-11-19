<?php

namespace App\Filament\Resources\LeaseAgreements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LeaseAgreementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lease_id')
                    ->relationship('lease', 'id')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('original_filename')
                    ->required(),
                Toggle::make('owner_signed')
                    ->required(),
                Toggle::make('tenant_signed')
                    ->required(),
                DateTimePicker::make('owner_signed_at'),
                DateTimePicker::make('tenant_signed_at'),
                TextInput::make('owner_signature_ip'),
                TextInput::make('tenant_signature_ip'),
            ]);
    }
}
