<?php

namespace App\Filament\Resources\SystemSettings;

use App\Filament\Concerns\HasModuleAccess;
use App\Filament\Resources\SystemSettings\Pages\CreateSystemSetting;
use App\Filament\Resources\SystemSettings\Pages\EditSystemSetting;
use App\Filament\Resources\SystemSettings\Pages\ListSystemSettings;
use App\Filament\Resources\SystemSettings\Pages\ViewSystemSetting;
use App\Filament\Resources\SystemSettings\Schemas\SystemSettingForm;
use App\Filament\Resources\SystemSettings\Tables\SystemSettingsTable;
use App\Models\SystemSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SystemSettingResource extends Resource
{
    use HasModuleAccess;

    protected static ?string $model = SystemSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static \UnitEnum|string|null $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'System Settings';

    protected static ?string $modelLabel = 'System Setting';

    protected static ?string $pluralModelLabel = 'System Settings';

    protected static ?int $navigationSort = 3;

    protected static string $accessModule = 'system';

    public static function form(Schema $schema): Schema
    {
        return SystemSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SystemSettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSystemSettings::route('/'),
            'create' => CreateSystemSetting::route('/create'),
            'edit' => EditSystemSetting::route('/{record}/edit'),
            'view' => ViewSystemSetting::route('/{record}'),
        ];
    }

    /**
     * Normalise form data before persisting:
     *  - decode JSON `options` into an array
     *  - encrypt the value when required, avoiding double encryption
     */
    public static function normalizeData(array $data, ?string $originalValue = null): array
    {
        if (isset($data['options']) && is_string($data['options'])) {
            $decoded = json_decode($data['options'], true);
            $data['options'] = is_array($decoded) ? $decoded : null;
        }

        if (! empty($data['is_encrypted']) && filled($data['value'] ?? null)) {
            $keepOriginal = false;

            if ($originalValue !== null) {
                try {
                    $keepOriginal = decrypt($originalValue) === $data['value'];
                } catch (\Throwable) {
                    $keepOriginal = false;
                }
            }

            $data['value'] = $keepOriginal ? $originalValue : encrypt($data['value']);
        }

        return $data;
    }
}
