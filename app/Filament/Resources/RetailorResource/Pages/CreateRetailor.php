<?php

namespace App\Filament\Resources\RetailorResource\Pages;

use App\Filament\Resources\RetailorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRetailor extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    protected static string $resource = RetailorResource::class;
    public function getHeaderActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        return $data;
    }

}
