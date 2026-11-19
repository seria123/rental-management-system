<?php

namespace App\Filament\Resources\PropertyOwners\Pages;

use App\Filament\Resources\PropertyOwners\PropertyOwnerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPropertyOwner extends EditRecord
{
    protected static string $resource = PropertyOwnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
