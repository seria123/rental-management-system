<?php

namespace App\Filament\Resources\SystemSettings\Pages;

use App\Filament\Resources\SystemSettings\SystemSettingResource;
use Filament\Resources\Pages\EditRecord;

class EditSystemSetting extends EditRecord
{
    protected static string $resource = SystemSettingResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (! empty($data['is_encrypted']) && filled($data['value'] ?? null)) {
            try {
                $data['value'] = decrypt($data['value']);
            } catch (\Throwable) {
                // leave as-is if it cannot be decrypted
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return SystemSettingResource::normalizeData($data, $this->record?->getOriginal('value'));
    }
}
