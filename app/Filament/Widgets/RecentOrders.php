<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BrandResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\RetailorResource;
use App\Models\Order as ModelsOrder;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Order;
use Filament\Actions\ViewAction;

class RecentOrders extends TableWidget
{
    protected static ?string $heading = "Recent Today's Orders";
    public static ?int $sort = 4;

    protected function getTableDescription(): ?string
    {
        $today = Carbon::today('Asia/Kolkata')->format('d-M');
        return "Today's $today Order Summary";
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ModelsOrder::query()->whereDate('order_date', Carbon::today()))
            ->defaultSort('order_date', 'desc')
            ->columns([

                TextColumn::make('brand.name')
                    ->label('Brand')
                    ->icon('heroicon-s-arrow-top-right-on-square')
                    ->url(fn($record): string => BrandResource::getUrl("edit", ["record" => $record->brand_id]))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('retailor.name')
                    ->label('Retailer')
                    ->icon('heroicon-s-arrow-top-right-on-square')
                    ->url(fn($record): string => RetailorResource::getUrl("edit", ["record" => $record->retailor]))
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->numeric()
                    ->summarize(Sum::make()->label('Total Amount')->money(config('settings.general_settings.default_currency', 'INR')))
                    ->money(config("services.system_params.currency"))
                    ->tooltip(fn($record) =>  $record->order_date->format('Y-m-d H:i:s'))
                    ->sortable(),

                // TextColumn::make('order_date')
                //     ->label('Date')
                //     ->since()
                //     ->sortable(),
            ])
            ->paginated(0)
             ->recordUrl(
                fn (ModelsOrder $record): string => OrderResource::getUrl("edit", ['record' => $record]),
            )
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                // ViewAction::make()
                //     ->label('')
                //     ->url(fn($record): string => OrderResource::getUrl("edit", ["record" => $record->id]))
                //     ->icon('heroicon-o-pencil'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
