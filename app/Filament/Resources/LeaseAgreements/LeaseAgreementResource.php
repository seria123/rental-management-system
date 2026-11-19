<?php

namespace App\Filament\Resources\LeaseAgreements;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\LeaseAgreements\Pages\CreateLeaseAgreement;
use App\Filament\Resources\LeaseAgreements\Pages\EditLeaseAgreement;
use App\Filament\Resources\LeaseAgreements\Pages\ListLeaseAgreements;
use App\Filament\Resources\LeaseAgreements\Pages\ViewLeaseAgreement;
use App\Filament\Resources\LeaseAgreements\Schemas\LeaseAgreementForm;
use App\Filament\Resources\LeaseAgreements\Tables\LeaseAgreementsTable;
use App\Models\LeaseAgreement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LeaseAgreementResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = LeaseAgreement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static \UnitEnum|string|null $navigationGroup = 'Property Management';

    protected static ?string $navigationLabel = 'Lease Agreements';

    protected static ?int $navigationSort = 2;

    protected static string $accessModule = 'tenancy';

    public static function form(Schema $schema): Schema
    {
        return LeaseAgreementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LeaseAgreementsTable::configure($table);
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
            'index' => ListLeaseAgreements::route('/'),
            'create' => CreateLeaseAgreement::route('/create'),
            'edit' => EditLeaseAgreement::route('/{record}/edit'),
            'view' => ViewLeaseAgreement::route('/{record}'),
        ];
    }
}
