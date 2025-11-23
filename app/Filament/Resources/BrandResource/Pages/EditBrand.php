<?php

namespace App\Filament\Resources\BrandResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use Filament\Actions\DeleteAction;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\Filament\Resources\BrandResource;
use Filament\Actions;

class EditBrand extends \Filament\Resources\Pages\EditRecord
{
    use Translatable;
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            DeleteAction::make(),
        ];
    }
}