<?php

namespace App\Filament\Resources\PropertyOwners;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\PropertyOwners\Pages\CreatePropertyOwner;
use App\Filament\Resources\PropertyOwners\Pages\EditPropertyOwner;
use App\Filament\Resources\PropertyOwners\Pages\ListPropertyOwners;
use App\Filament\Resources\PropertyOwners\Pages\ViewPropertyOwner;
use App\Filament\Resources\PropertyOwners\Schemas\PropertyOwnerForm;
use App\Filament\Resources\PropertyOwners\Tables\PropertyOwnersTable;
use App\Models\PropertyOwner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PropertyOwnerResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = PropertyOwner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static \UnitEnum|string|null $navigationGroup = 'Property Management';

    protected static ?string $navigationLabel = 'Property Owners';

    protected static ?int $navigationSort = 3;

    protected static string $accessModule = 'properties';

    public static function form(Schema $schema): Schema
    {
        return PropertyOwnerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PropertyOwnersTable::configure($table);
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
            'index' => ListPropertyOwners::route('/'),
            'create' => CreatePropertyOwner::route('/create'),
            'edit' => EditPropertyOwner::route('/{record}/edit'),
            'view' => ViewPropertyOwner::route('/{record}'),
        ];
    }
}
