<?php

namespace App\Filament\Resources\MoveOutRequests;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\MoveOutRequests\Pages\CreateMoveOutRequest;
use App\Filament\Resources\MoveOutRequests\Pages\EditMoveOutRequest;
use App\Filament\Resources\MoveOutRequests\Pages\ListMoveOutRequests;
use App\Filament\Resources\MoveOutRequests\Pages\ViewMoveOutRequest;
use App\Filament\Resources\MoveOutRequests\Schemas\MoveOutRequestForm;
use App\Filament\Resources\MoveOutRequests\Tables\MoveOutRequestsTable;
use App\Models\MoveOutRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MoveOutRequestResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = MoveOutRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowRightOnRectangle;

    protected static \UnitEnum|string|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Move-out Requests';

    protected static ?int $navigationSort = 2;

    protected static string $accessModule = 'operations';

    public static function form(Schema $schema): Schema
    {
        return MoveOutRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MoveOutRequestsTable::configure($table);
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
            'index' => ListMoveOutRequests::route('/'),
            'create' => CreateMoveOutRequest::route('/create'),
            'edit' => EditMoveOutRequest::route('/{record}/edit'),
            'view' => ViewMoveOutRequest::route('/{record}'),
        ];
    }
}
