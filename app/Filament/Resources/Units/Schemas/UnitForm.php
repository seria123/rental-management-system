<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Unit Information')
                    ->schema([
                        Select::make('property_id')
                            ->relationship('property', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('unit_number')
                            ->required()
                            ->maxLength(255),
                        Select::make('unit_type')
                            ->options([
                                'studio' => 'Studio',
                                'one_bedroom' => 'One bedroom',
                                'two_bedroom' => 'Two bedroom',
                                'three_bedroom' => 'Three bedroom',
                                'office' => 'Office',
                                'commercial' => 'Commercial',
                            ])
                            ->required(),
                        TextInput::make('floor')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Rent and Deposit')
                    ->schema([
                        TextInput::make('monthly_rent')
                            ->numeric()
                            ->prefix('KES')
                            ->required(),
                        TextInput::make('deposit_amount')
                            ->numeric()
                            ->prefix('KES')
                            ->required(),
                        TextInput::make('rent_amount')
                            ->numeric()
                            ->prefix('KES'),
                        Select::make('status')
                            ->options([
                                'vacant' => 'Vacant',
                                'occupied' => 'Occupied',
                                'maintenance' => 'Maintenance',
                            ])
                            ->required()
                            ->default('vacant'),
                    ])
                    ->columns(2),

                Section::make('Additional Details')
                    ->schema([
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('lease_agreement_path')
                            ->hidden(),
                        TextInput::make('lease_agreement_filename')
                            ->hidden(),
                    ]),
            ]);
    }
}
