<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // dump($data);
        //json store retailors details
        $data['customer_information'] = [
                'customer_name'     =>$data['customer_information']['customer_name'] ?? null,
                'customer_phone'    =>$data['customer_information']['customer_phone'] ?? null,
                'customer_email'    =>$data['customer_information']['customer_email'] ?? null,
                'customer_address'  =>$data['customer_information']['customer_address'] ?? null,
            ];

        // Optionally, unset the flat fields so they don't get stored twice
        unset(
            $data['customer_name'],
            $data['customer_phone'],
            $data['customer_email'],
            $data['customer_address']
        );
        // dd($data);
        return $data;

    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // dd($data);
        // Transform individual customer fields to JSON
        if (isset($data['customer_information'])) {
            $data['customer_information'] = json_encode($data['customer_information']);
        }
        // Transform individual order fields to
        return $data;
    }
}
