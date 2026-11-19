<?php

namespace App\Filament\Resources\Announcements;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\Announcements\Pages\CreateAnnouncement;
use App\Filament\Resources\Announcements\Pages\EditAnnouncement;
use App\Filament\Resources\Announcements\Pages\ListAnnouncements;
use App\Filament\Resources\Announcements\Pages\ViewAnnouncement;
use App\Filament\Resources\Announcements\Schemas\AnnouncementForm;
use App\Filament\Resources\Announcements\Tables\AnnouncementsTable;
use App\Models\Announcement;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AnnouncementResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = Announcement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static \UnitEnum|string|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Announcements';

    protected static ?int $navigationSort = 4;

    protected static string $accessModule = 'operations';

    public static function getModelLabel(): string
    {
        return __('Announcement');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Announcements');
    }

    public static function getNavigationLabel(): string
    {
        return __('Announcements');
    }

    public static function form(Schema $schema): Schema
    {
        return AnnouncementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AnnouncementsTable::configure($table);
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
            'index' => ListAnnouncements::route('/'),
            'create' => CreateAnnouncement::route('/create'),
            'edit' => EditAnnouncement::route('/{record}/edit'),
            'view' => ViewAnnouncement::route('/{record}'),
        ];
    }
}
