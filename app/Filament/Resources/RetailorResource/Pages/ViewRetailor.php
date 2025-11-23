<?php

namespace App\Filament\Resources\RetailorResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;
use Filament\Actions\EditAction;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\Filament\Resources\RetailorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRetailor extends ViewRecord
{
    use Translatable;
    protected static string $resource = RetailorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
