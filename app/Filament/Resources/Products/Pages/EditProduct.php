<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    // protected function beforeSave(): void
    // {
    //     $state = $this->form->getState(); // dapatkan seluruh state form
    //     // dd($state);

    //     // contoh cepat:
    //     // dd($state['is_active'] ?? null);
    // }

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     // dd($data);
    // }
    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
