<?php

namespace App\Filament\Resources\SupportTickets;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\SupportTickets\Pages\CreateSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\EditSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use App\Filament\Resources\SupportTickets\Schemas\SupportTicketForm;
use App\Filament\Resources\SupportTickets\Tables\SupportTicketsTable;
use App\Models\SupportTicket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = SupportTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static \UnitEnum|string|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Support Tickets';

    protected static ?int $navigationSort = 3;

    protected static string $accessModule = 'operations';

    public static function form(Schema $schema): Schema
    {
        return SupportTicketForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SupportTicketsTable::configure($table);
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
            'index' => ListSupportTickets::route('/'),
            'create' => CreateSupportTicket::route('/create'),
            'edit' => EditSupportTicket::route('/{record}/edit'),
            'view' => ViewSupportTicket::route('/{record}'),
        ];
    }
}
