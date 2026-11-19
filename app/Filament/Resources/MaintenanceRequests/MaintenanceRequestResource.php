<?php

namespace App\Filament\Resources\MaintenanceRequests;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\MaintenanceRequests\Pages\CreateMaintenanceRequest;
use App\Filament\Resources\MaintenanceRequests\Pages\EditMaintenanceRequest;
use App\Filament\Resources\MaintenanceRequests\Pages\ListMaintenanceRequests;
use App\Filament\Resources\MaintenanceRequests\Pages\ViewMaintenanceRequest;
use App\Filament\Resources\MaintenanceRequests\Schemas\MaintenanceRequestForm;
use App\Filament\Resources\MaintenanceRequests\Tables\MaintenanceRequestsTable;
use App\Models\MaintenanceRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MaintenanceRequestResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = MaintenanceRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static \UnitEnum|string|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Maintenance Requests';

    protected static ?int $navigationSort = 1;

    protected static string $accessModule = 'operations';

    public static function form(Schema $schema): Schema
    {
        return MaintenanceRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MaintenanceRequestsTable::configure($table);
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
            'index' => ListMaintenanceRequests::route('/'),
            'create' => CreateMaintenanceRequest::route('/create'),
            'edit' => EditMaintenanceRequest::route('/{record}/edit'),
            'view' => ViewMaintenanceRequest::route('/{record}'),
        ];
    }
}
