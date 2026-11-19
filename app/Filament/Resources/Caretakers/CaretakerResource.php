<?php

namespace App\Filament\Resources\Caretakers;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\Caretakers\Pages\CreateCaretaker;
use App\Filament\Resources\Caretakers\Pages\EditCaretaker;
use App\Filament\Resources\Caretakers\Pages\ListCaretakers;
use App\Filament\Resources\Caretakers\Pages\ViewCaretaker;
use App\Filament\Resources\Caretakers\Schemas\CaretakerForm;
use App\Filament\Resources\Caretakers\Tables\CaretakersTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CaretakerResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static \UnitEnum|string|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Caretakers';

    protected static ?int $navigationSort = 5;

    protected static string $accessModule = 'system';

    public static function getModelLabel(): string
    {
        return __('Caretaker');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Caretakers');
    }

    public static function getNavigationLabel(): string
    {
        return __('Caretakers');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', User::ROLE_CARETAKER);
    }

    public static function form(Schema $schema): Schema
    {
        return CaretakerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CaretakersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaretakers::route('/'),
            'create' => CreateCaretaker::route('/create'),
            'edit' => EditCaretaker::route('/{record}/edit'),
            'view' => ViewCaretaker::route('/{record}'),
        ];
    }
}
