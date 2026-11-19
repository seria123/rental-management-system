<?php

namespace App\Filament\Resources\Agents;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\Agents\Pages\CreateAgent;
use App\Filament\Resources\Agents\Pages\EditAgent;
use App\Filament\Resources\Agents\Pages\ListAgents;
use App\Filament\Resources\Agents\Pages\ViewAgent;
use App\Filament\Resources\Agents\Schemas\AgentForm;
use App\Filament\Resources\Agents\Tables\AgentsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AgentResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static \UnitEnum|string|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Agents';

    protected static ?int $navigationSort = 4;

    protected static string $accessModule = 'system';

    public static function getModelLabel(): string
    {
        return __('Agent');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Agents');
    }

    public static function getNavigationLabel(): string
    {
        return __('Agents');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', User::ROLE_AGENT);
    }

    public static function form(Schema $schema): Schema
    {
        return AgentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AgentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgents::route('/'),
            'create' => CreateAgent::route('/create'),
            'edit' => EditAgent::route('/{record}/edit'),
            'view' => ViewAgent::route('/{record}'),
        ];
    }
}
