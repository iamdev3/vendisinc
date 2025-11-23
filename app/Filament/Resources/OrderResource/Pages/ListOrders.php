<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Enums\OrderStatus;
use Filament\Actions\CreateAction;
use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {   

        $tabs = [
            'all' => Tab::make('All')
                    ->badge(fn () => $this->getModel()::count()),
        ];

        // Loop through enum cases to create tabs
        foreach (OrderStatus::cases() as $status) {

            $tabs[$status->value] = 

                Tab::make($status->label())
                    ->modifyQueryUsing(fn (Builder $query) => $query->where('status', $status->value))
                    ->badge(fn () => $this->getModel()::where('status', $status->value)->count())
                    ->badgeColor($status->color())
                    ->icon($status->icon());
        }

        return $tabs;

    }

    
}
