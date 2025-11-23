<?php

namespace App\Filament\Resources\BrandResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;
use Filament\Actions\CreateAction;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use App\Filament\Resources\BrandResource;
use Filament\Actions;

class ListBrands extends \Filament\Resources\Pages\ListRecords
{
    use Translatable;
    protected static string $resource = BrandResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}