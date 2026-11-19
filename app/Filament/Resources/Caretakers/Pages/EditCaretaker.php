<?php

namespace App\Filament\Resources\Caretakers\Pages;

use App\Filament\Resources\Caretakers\CaretakerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCaretaker extends EditRecord
{
    protected static string $resource = CaretakerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
