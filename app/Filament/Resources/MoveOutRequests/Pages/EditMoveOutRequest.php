<?php

namespace App\Filament\Resources\MoveOutRequests\Pages;

use App\Filament\Resources\MoveOutRequests\MoveOutRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMoveOutRequest extends EditRecord
{
    protected static string $resource = MoveOutRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
