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
        // Handle customer_information JSON decoding
        if (isset($data['customer_information']) && is_string($data['customer_information'])) {
            $decodedCustomerInfo = json_decode($data['customer_information'], true);

            // If decoding was successful and we have an array
            if (is_array($decodedCustomerInfo)) {
                $data['customer_information'] = $decodedCustomerInfo;

            } else {
                // If JSON decoding failed or data is malformed, set empty array
                $data['customer_information'] = [
                    'customer_name'     => null,
                    'customer_phone'    => null,
                    'customer_email'    => null,
                    'customer_address'  => null,
                ];
            }

        } elseif (!isset($data['customer_information'])) {
            // If customer_information doesn't exist, create empty structure
            $data['customer_information'] = [
                'customer_name'     => null,
                'customer_phone'    => null,
                'customer_email'    => null,
                'customer_address'  => null,
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Transform customer_information array to JSON string for database storage
        if (isset($data['customer_information']) && is_array($data['customer_information'])) {
            $data['customer_information'] = json_encode($data['customer_information']);
        }

        return $data;
    }
}
