<?php

namespace App\Filament\Resources\PropertyOwners\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PropertyOwnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
            ]);
    }
}
