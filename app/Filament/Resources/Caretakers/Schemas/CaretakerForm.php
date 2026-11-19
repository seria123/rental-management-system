<?php

namespace App\Filament\Resources\Caretakers\Schemas;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CaretakerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('active'),
                TextInput::make('password')
                    ->password()
                    ->required()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null),
            ]);
    }

    public static function mutateFormData(array $data): array
    {
        $data['role'] = User::ROLE_CARETAKER;

        return $data;
    }
}
