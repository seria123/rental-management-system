<?php

namespace App\Filament\Resources\Caretakers\Pages;

use App\Filament\Resources\Caretakers\CaretakerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCaretakers extends ListRecords
{
    protected static string $resource = CaretakerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
