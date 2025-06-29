<?php

namespace App\Filament\Resources\RetailorResource\Pages;

use App\Filament\Resources\RetailorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRetailor extends EditRecord
{
    use EditRecord\Concerns\Translatable;
    protected static string $resource = RetailorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // dd($data);
        return $data;
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // dd($data);
        return $data;
    }


}
