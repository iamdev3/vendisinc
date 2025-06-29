<?php

namespace App\Filament\Resources\RetailorResource\Pages;

use App\Filament\Resources\RetailorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRetailors extends ListRecords
{
    protected static string $resource = RetailorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
