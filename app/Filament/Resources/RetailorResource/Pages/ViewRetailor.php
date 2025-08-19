<?php

namespace App\Filament\Resources\RetailorResource\Pages;

use App\Filament\Resources\RetailorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRetailor extends ViewRecord
{
    use ViewRecord\Concerns\Translatable;
    protected static string $resource = RetailorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\LocaleSwitcher::make(),
        ];
    }
}
