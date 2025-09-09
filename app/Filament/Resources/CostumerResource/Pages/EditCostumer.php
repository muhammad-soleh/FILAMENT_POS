<?php

namespace App\Filament\Resources\CostumerResource\Pages;

use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\CostumerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCostumer extends EditRecord
{
    protected static string $resource = CostumerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
