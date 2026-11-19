<?php

namespace App\Filament\Resources\Notifications;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\Notifications\Pages\ListNotifications;
use App\Filament\Resources\Notifications\Pages\ViewNotification;
use App\Filament\Resources\Notifications\Tables\NotificationsTable;
use App\Models\AppNotification;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class NotificationResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = AppNotification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static \UnitEnum|string|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $modelLabel = 'Notification';

    protected static ?string $pluralModelLabel = 'Notifications';

    protected static ?int $navigationSort = 5;

    protected static string $accessModule = 'operations';

    public static function table(Table $table): Table
    {
        return NotificationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNotifications::route('/'),
            'view' => ViewNotification::route('/{record}'),
        ];
    }
}
