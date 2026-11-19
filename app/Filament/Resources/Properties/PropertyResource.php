<?php

namespace App\Filament\Resources\Properties;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\Properties\Pages\CreateProperty;
use App\Filament\Resources\Properties\Pages\EditProperty;
use App\Filament\Resources\Properties\Pages\ListProperties;
use App\Filament\Resources\Properties\Pages\ViewProperty;
use App\Filament\Resources\Properties\Schemas\PropertyForm;
use App\Filament\Resources\Properties\Tables\PropertiesTable;
use App\Models\Property;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PropertyResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = Property::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static \UnitEnum|string|null $navigationGroup = 'Property Management';

    protected static ?string $navigationLabel = 'Properties';

    protected static ?int $navigationSort = 1;

    protected static string $accessModule = 'properties';

    public static function form(Schema $schema): Schema
    {
        return PropertyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PropertiesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProperties::route('/'),
            'create' => CreateProperty::route('/create'),
            'edit' => EditProperty::route('/{record}/edit'),
            'view' => ViewProperty::route('/{record}'),
        ];
    }
}
