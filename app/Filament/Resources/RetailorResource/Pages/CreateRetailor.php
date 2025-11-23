<?php

namespace App\Filament\Resources\RetailorResource\Pages;

use LaraZeus\SpatieTranslatable\Resources\Pages\CreateRecord\Concerns\Translatable;
use LaraZeus\SpatieTranslatable\Actions\LocaleSwitcher;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\RetailorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRetailor extends CreateRecord
{
    use Translatable;

    protected static string $resource = RetailorResource::class;
    public function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);

        //add current auth user id
        $data['user_id'] = Auth::id();

        //json store retailors details
        $data['customer_information'] = [
                'customer_name'     => $data['customer_name'] ?? null,
                'customer_phone'    => $data['customer_phone'] ?? null,
                'customer_email'    => $data['customer_email'] ?? null,
                'customer_address'  => $data['customer_address'] ?? null,
            ];
            // Optionally, unset the flat fields so they don't get stored twice
            unset(
                $data['customer_name'],
                $data['customer_phone'],
                $data['customer_email'],
                $data['customer_address']
            );

        return $data;
    }



}
