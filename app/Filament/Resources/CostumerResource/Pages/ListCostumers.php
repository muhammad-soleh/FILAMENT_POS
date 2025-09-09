<?php

namespace App\Filament\Resources\CostumerResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\CostumerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCostumers extends ListRecords
{
    protected static string $resource = CostumerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
