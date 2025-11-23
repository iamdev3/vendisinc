<?php

namespace App\Filament\Resources\BrandResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\ViewRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\Filament\Resources\BrandResource;
use Filament\Actions;

class ViewBrand extends \Filament\Resources\Pages\ViewRecord
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