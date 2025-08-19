<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Container\Attributes\Auth;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // dd($data);
        // Set the current user as the creator
        $data['user_id'] = \Illuminate\Support\Facades\Auth::id();

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

}
