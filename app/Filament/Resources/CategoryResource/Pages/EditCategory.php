<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\CategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;

class EditCategory extends EditRecord
{   
    use Translatable;
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            LocaleSwitcher::make(),
        ];
    }
}
