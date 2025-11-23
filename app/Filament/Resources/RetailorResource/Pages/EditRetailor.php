<?php

namespace App\Filament\Resources\RetailorResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use Filament\Actions\DeleteAction;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\Filament\Resources\RetailorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRetailor extends EditRecord
{
    use Translatable;
    protected static string $resource = RetailorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            LocaleSwitcher::make(),
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
