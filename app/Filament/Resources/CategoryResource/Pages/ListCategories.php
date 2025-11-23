<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class ListCategories extends ListRecords
{   
    use Translatable;
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label(__("new_category")),
            LocaleSwitcher::make(),
        ];
    }
}
