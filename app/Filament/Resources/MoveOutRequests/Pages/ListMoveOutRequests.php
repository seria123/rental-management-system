<?php

namespace App\Filament\Resources\MoveOutRequests\Pages;

use App\Filament\Resources\MoveOutRequests\MoveOutRequestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMoveOutRequests extends ListRecords
{
    protected static string $resource = MoveOutRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
