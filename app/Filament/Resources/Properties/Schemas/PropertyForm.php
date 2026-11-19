<?php

namespace App\Filament\Resources\Properties\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PropertyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Property Information')
                    ->description('Core information used across the property portfolio.')
                    ->schema([
                        Select::make('property_owner_id')
                            ->label('Property owner')
                            ->relationship('owner', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Property name')
                            ->required()
                            ->maxLength(255),
                        Select::make('property_type')
                            ->label('Property type')
                            ->options([
                                'residential' => 'Residential',
                                'commercial' => 'Commercial',
                                'mixed_use' => 'Mixed use',
                            ])
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Location Details')
                    ->description('The main location and address for this property.')
                    ->schema([
                        TextInput::make('city')
                            ->label('County / city')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Physical address')
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Property Status')
                    ->schema([
                        Select::make('status')
                            ->label('Property status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'maintenance' => 'Under maintenance',
                            ])
                            ->required()
                            ->default('active'),
                    ])
                    ->columns(2),
            ]);
    }
}
