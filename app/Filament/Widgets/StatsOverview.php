<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\BrandResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{

    protected ?string $heading     = null;
    protected ?string $description = null;

    public function __construct()
    {
        $this->heading      = __('Business Overview');
        $this->description  = __('Key metrics and statistics for your business');
    }

    protected function getStats(): array
    {
        $totalBrand     = Brand::where("is_active", 1)->count();
        $totalProduct   = Product::count();
        $totalCategory  = Category::count();
        $totalOrder     = Order::count();

        // Calculate total pending order amount (orders with status 'pending')
        $totalPendingOrderAmount = Order::where('payment_status', 'pending')->sum('total_amount');

        // Calculate orders in progress (confirmed or shipped - not yet delivered)
        $ordersInProgress = Order::whereIn('status', ['confirmed', 'shipped'])->count();

        // Calculate total profit from all delivered orders
        $totalProfit = Order::where('payment_status', 'paid')->sum('total_profit');

        $stats = [];

        $statsDetails = [

            [
                'label'             => __("Total Active Brands"),
                'value'             => $totalBrand,
                'value_type' => 'number',
                'shortDescription'  => __("Active brand partnerships"),
                'description'       => __("Total number of brands registered in the system. Only active brands are counted in business metrics."),
                'icon'              => "heroicon-m-building-storefront",
                'color'             => "success",
                'url'               => BrandResource::getUrl()
            ],
            [
                'label'             => __("Total Products"),
                'value'             => $totalProduct,
                'value_type'        => 'number',
                'shortDescription'  => __("Products in catalog"),
                'description'       => __("Total number of products across all brands. This includes both active and inactive products."),
                'icon'              => "heroicon-m-archive-box",
                'color'             => "info",
                'url'               => ProductResource::getUrl()

            ],
             [
                'label'             => __("Total Orders"),
                'value'             => $totalOrder,
                'value_type'        => 'number',
                'shortDescription'  => __("All order records"),
                'description'       => __("Total number of orders placed, including pending, confirmed, shipped, delivered, cancelled, and on-hold orders."),
                'icon'              => "heroicon-m-shopping-cart",
                'color'             => "primary",
                'url'               => OrderResource::getUrl()

            ],
            [
                'label'             => __("Orders in Progress"),
                'value'             => $ordersInProgress,
                'value_type'        => 'number',
                'shortDescription'  => __("Confirmed & shipped orders"),
                'description'       => __("Number of orders that are confirmed or shipped but not yet delivered. These orders are currently in transit or being processed for delivery."),
                'icon'              => "heroicon-m-truck",
                'color'             => "info",
                'url'               => OrderResource::getUrl()."?filters[status][value]=confirmed,shipped&filters[status][condition]=in&filters[status][values][0]=confirmed&filters[status][values][1]=shipped"

            ],
            [
                'label'             => __("Pending Order Value"),
                'value'             => $totalPendingOrderAmount,
                'value_type'        => 'amount',
                'shortDescription'  => __("Value of pending orders"),
                'description'       => __("Total monetary value of orders with 'pending' as payment status. These orders are awaiting to be paid."),
                'icon'              => "heroicon-m-clock",
                'color'             => "warning",
                'url'               => OrderResource::getUrl()."?filters[payment_status][value]=pending"

            ],
            [
                'label'             => __("Total Profit"),
                'value'             => $totalProfit,
                'value_type'        => 'amount',
                'shortDescription'  => __("Profit from delivered orders"),
                'description'       => __("Total profit generated from delivered orders & payment status mark as paid. Profit is calculated as sell price minus base cost for each product."),
                'icon'              => "heroicon-m-currency-rupee",
                'color'             => "success",
                'url'               => OrderResource::getUrl()."?filters[payment_status][value]=paid"

            ],
            [
                'label'             => __("Total Categories"),
                'value'             => $totalCategory,
                'value_type'        => 'number',
                'shortDescription'  => __("Product categories"),
                'description'       => __("Number of product categories used to organize products. Categories help in better product management."),
                'icon'              => "heroicon-m-tag",
                'color'             => "warning",
                'url'               => CategoryResource::getUrl()

            ],



        ];

        foreach ($statsDetails as $stat => $statValues) {

            $newStat = Stat::make($statValues['label'],  self::valueHelper($statValues['value'], $statValues['value_type']))
                ->description($statValues['shortDescription'])
                ->descriptionIcon($statValues['icon'] ?? '')
                ->color($statValues['color'] ?? "info")
                ->url($statValues['url'] ?? "")
                ->openUrlInNewTab()
                ->extraAttributes([
                    'class'     => 'cursor-pointer',
                    'title'     => $statValues['description'] ?? $statValues['label'], // optional tooltip
                ]);

            $stats[] = $newStat; // append to array
        }

        return $stats;

    }

    protected static function valueHelper($value, $type = 'number')
    {

        $currency = config('settings.general_settings.default_currency', 'INR');
        $currency = match($currency){
            "INR"   => '₹',
            "USD"   => '$',
            default => '₹',
        };

        return match ($type) {
            'amount'     => $currency . number_format($value, 2),
            'percentage' => number_format($value, 2) . '%',
            default      => $value,
        };
    }
}
