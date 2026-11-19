<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('name')->required()->maxLength(255),
                        TextInput::make('id_number')->label('National ID / ID number')->required(),
                        Select::make('gender')->options(['male' => 'Male', 'female' => 'Female', 'other' => 'Other']),
                        DatePicker::make('date_of_birth')->native(false),
                        TextInput::make('occupation'),
                    ])
                    ->columns(2),

                Section::make('Contact Details')
                    ->schema([
                        TextInput::make('phone')->tel(),
                        TextInput::make('email')->label('Email address')->email(),
                        TextInput::make('emergency_contact_name'),
                        TextInput::make('emergency_contact_phone')->tel(),
                        Textarea::make('address')->rows(3)->columnSpanFull(),
                        TextInput::make('national_id')->label('National ID record'),
                    ])
                    ->columns(2),

                Section::make('Occupancy and Status')
                    ->schema([
                        Select::make('unit_id')
                            ->relationship('unit', 'unit_number')
                            ->searchable()
                            ->preload(),
                        TextInput::make('month')
                            ->helperText('Optional tenancy period identifier.'),
                        Select::make('status')
                            ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                            ->required()
                            ->default('active'),
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload(),
                    ])
                    ->columns(2),
            ]);
    }
}
