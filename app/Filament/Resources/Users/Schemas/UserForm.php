<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Select::make('role')
                    ->label('Role')
                    ->required()
                    ->options([
                        User::ROLE_ADMIN => 'Administrator',
                        User::ROLE_PROPERTY_MANAGER => 'Property Manager',
                        User::ROLE_ACCOUNTANT => 'Accountant',
                        User::ROLE_CARETAKER => 'Caretaker',
                        User::ROLE_TENANT => 'Tenant',
                        User::ROLE_DEVELOPER => 'Developer',
                        User::ROLE_OWNER => 'Owner',
                        User::ROLE_AGENT => 'Agent',
                    ])
                    ->default(User::ROLE_PROPERTY_MANAGER),
                Select::make('status')
                    ->label('Status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),
                DateTimePicker::make('email_verified_at')
                    ->label('Email Verified At'),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                    ->maxLength(255),
            ]);
    }
}
