<?php

namespace App\Filament\Resources\Announcements\Schemas;

use App\Models\Tenant;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Type')
                    ->required()
                    ->options([
                        'general' => 'General',
                        'maintenance' => 'Maintenance',
                        'security' => 'Security',
                        'meeting' => 'Property Meeting',
                        'utility' => 'Utility Notice',
                    ])
                    ->default('general'),
                Select::make('target_type')
                    ->label('Target')
                    ->required()
                    ->options([
                        'all' => 'All Tenants',
                        'property' => 'Specific Property',
                        'unit' => 'Specific Unit',
                        'tenant' => 'Specific Tenant',
                    ])
                    ->live()
                    ->default('all'),
                Select::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'name')
                    ->visible(fn (Get $get): bool => $get('target_type') === 'property'),
                Select::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'unit_number')
                    ->visible(fn (Get $get): bool => $get('target_type') === 'unit'),
                Select::make('tenant_id')
                    ->label('Tenant')
                    ->options(fn () => Tenant::query()->pluck('name', 'id'))
                    ->searchable()
                    ->visible(fn (Get $get): bool => $get('target_type') === 'tenant'),
                DateTimePicker::make('published_at')
                    ->label('Publish At'),
                DateTimePicker::make('expires_at')
                    ->label('Expires At'),
                Toggle::make('is_pinned')
                    ->label('Pinned')
                    ->default(false),
            ]);
    }
}
