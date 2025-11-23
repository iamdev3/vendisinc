<?php

namespace App\Filament\Resources\BrandResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\Filament\Resources\BrandResource;
use Filament\Actions;

class CreateBrand extends \Filament\Resources\Pages\CreateRecord
{
    use Translatable;
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }
}