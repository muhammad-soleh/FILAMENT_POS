<?php

namespace App\Filament\Resources\CostumerResource\Pages;

use Filament\Actions\EditAction;
use App\Filament\Resources\CostumerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCostumer extends ViewRecord
{
    protected static string $resource = CostumerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
