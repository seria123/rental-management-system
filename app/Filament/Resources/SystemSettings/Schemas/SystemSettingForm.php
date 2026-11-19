<?php

namespace App\Filament\Resources\SystemSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SystemSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Unique identifier used by the application, e.g. mpesa.shortcode'),
                TextInput::make('group')
                    ->required()
                    ->default('general')
                    ->maxLength(255),
                Select::make('input_type')
                    ->options([
                        'text' => 'Text',
                        'textarea' => 'Textarea',
                        'number' => 'Number',
                        'boolean' => 'Boolean',
                        'checkbox' => 'Checkbox',
                        'select' => 'Select',
                        'password' => 'Password',
                    ])
                    ->default('text')
                    ->required(),
                Textarea::make('value')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('options')
                    ->label('Options (JSON)')
                    ->helperText('Only for select inputs. Provide a JSON object of value => label.')
                    ->rows(2)
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT) : $state)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->rows(2)
                    ->columnSpanFull(),
                Toggle::make('is_encrypted')
                    ->label('Encrypt value at rest'),
            ]);
    }
}
