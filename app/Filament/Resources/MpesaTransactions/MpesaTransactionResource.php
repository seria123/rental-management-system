<?php

namespace App\Filament\Resources\MpesaTransactions;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\MpesaTransactions\Pages\EditMpesaTransaction;
use App\Filament\Resources\MpesaTransactions\Pages\ListMpesaTransactions;
use App\Filament\Resources\MpesaTransactions\Pages\ViewMpesaTransaction;
use App\Filament\Resources\MpesaTransactions\Schemas\MpesaTransactionForm;
use App\Filament\Resources\MpesaTransactions\Tables\MpesaTransactionsTable;
use App\Models\MpesaTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MpesaTransactionResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = MpesaTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoneArrowUpRight;

    protected static \UnitEnum|string|null $navigationGroup = 'Financial Management';

    protected static ?string $navigationLabel = 'M-Pesa Transactions';

    protected static ?string $modelLabel = 'M-Pesa transaction';

    protected static ?string $pluralModelLabel = 'M-Pesa transactions';

    protected static ?int $navigationSort = 2;

    protected static string $accessModule = 'finance';

    public static function form(Schema $schema): Schema
    {
        return MpesaTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MpesaTransactionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMpesaTransactions::route('/'),
            'view' => ViewMpesaTransaction::route('/{record}'),
            'edit' => EditMpesaTransaction::route('/{record}/edit'),
        ];
    }
}
