<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use App\Models\Product;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Group;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\OrderStatus;
use App\Models\Brand;
use App\Models\Retailor;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Enums\FiltersLayout;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static string | \UnitEnum | null $navigationGroup = "Orders";
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-square-3-stack-3d';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Primary Order Information - Full width on mobile, left side on desktop
                Section::make('Order Information')
                    ->schema([
                        Select::make('retailer_id')
                            ->relationship('retailor', 'name')
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state, $operation) {
                                if ($operation == 'create' && $state) {
                                    $retailorDetails = Retailor::find($state);
                                    if ($retailorDetails) {
                                        $set('customer_information.customer_name', $retailorDetails->name ?? null);
                                        $set('customer_information.customer_phone', $retailorDetails->phone ?? null);
                                        $set('customer_information.customer_email', $retailorDetails->email ?? null);
                                        $set('customer_information.customer_address', $retailorDetails->address ?? null);
                                    }
                                }
                            })
                            ->disabledOn("edit")
                            ->searchable()
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        TextInput::make('order_number')
                            ->required()
                            ->disabledOn("edit")
                            ->visibleOn("edit")
                            ->maxLength(255)
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabledOn("edit")
                            ->visibleOn("edit")
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Select::make('brand_id')
                            ->label("Brand")
                            ->required()
                            ->preload()
                            ->disabledOn("edit")
                            ->searchable()
                            ->placeholder("Select a Brand")
                            ->relationship('brand', 'name')
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        DateTimePicker::make('order_date')
                            ->native(false)
                            ->label("Order Date")
                            ->disabledOn("edit")
                            ->default(now())
                            ->prefixicon('heroicon-o-calendar')
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        DateTimePicker::make('delivered_at')
                            ->disabledOn("create")
                            ->label("Delivered Date")
                            ->native(false)
                            ->prefixicon('heroicon-o-calendar')
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Textarea::make('notes')
                            ->label("Note for Retailor")
                            ->rows(3),
                        // ->columnSpanFull(),

                        Textarea::make('internal_notes')
                            ->label("Internal Notes")
                            ->rows(3),
                        // ->columnSpanFull(),

                        // Status fields - Stack on mobile, inline on desktop
                        // Group::make([
                        Radio::make('status')
                            ->options(OrderStatus::getOptions())
                            ->inline()
                            ->inlinelabel(false)
                            ->columnSpanFull()
                            ->default(OrderStatus::CONFIRMED)
                            ->required(),
                        // ])->columnSpanFull(),

                        // Group::make([
                        Radio::make('payment_status')
                            ->options([
                                'pending' => "Pending",
                                'paid'    => "Paid",
                            ])
                            ->default("pending")
                            ->inline()
                            ->inlineLabel(false)
                            ->required(),
                        // ])->columnSpanFull(),

                        Select::make('payment_method')
                            ->label("Payment Method")
                            ->preload()
                            ->searchable()
                            ->options([
                                'bank'    => "Bank",
                                'cash'    => "Cash",
                                'card'    => "Card",
                                'upi'     => "UPI",
                                'cheque'  => "Cheque",
                            ])
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),
                    ])
                    ->columns([
                        'sm' => 2,
                        'md' => 2,
                        'lg' => 2,
                    ])
                    ->columnSpan([
                        'sm' => 'full',
                        'md' => 'full',
                        'lg' => 4,
                    ]),

                // Customer Information - Full width on mobile, right side on desktop
                Section::make('Customer Details')
                    ->schema([
                        TextInput::make('customer_information.customer_name')
                            ->label("Customer Name")
                            ->prefixicon("heroicon-o-user")
                            ->disabledOn("edit")
                            ->maxLength(255)
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 2,
                                'lg' => 2,
                            ]),

                        TextInput::make('customer_information.customer_phone')
                            ->label("Customer Phone")
                            ->tel()
                            ->disabledOn("edit")
                            ->prefixicon('heroicon-o-phone')
                            ->maxLength(255)
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 2,
                                'lg' => 2,
                            ]),

                        TextInput::make('customer_information.customer_email')
                            ->label("Customer Email")
                            ->email()
                            ->disabledOn("edit")
                            ->prefixicon('heroicon-o-envelope')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Textarea::make('customer_information.customer_address')
                            ->label("Customer Address")
                            ->rows(3)
                            ->disabledOn("edit")
                            ->columnSpanFull(),
                    ])
                    ->columns([
                        'sm' => 2,
                        'md' => 2,
                        'lg' => 2,
                    ])
                    ->columnSpan([
                        'sm' => 'full',
                        'md' => 'full',
                        'lg' => 2,
                    ]),

                // Order Items - Always full width
                Section::make('Order Items')
                    ->schema([
                        Repeater::make('orderItems')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            $unitPrice = $product->sell_price ?? 0;
                                            $basePrice = $product->base_price ?? 0;
                                            $quantity = $get('quantity') ?? 1;

                                            $set('unit_price', $unitPrice);
                                            $set('total_price', $unitPrice * $quantity);

                                            // Calculate profit
                                            $unitProfit = $unitPrice - $basePrice;
                                            $set('unit_profit', $unitProfit);
                                            $set('total_profit', $unitProfit * $quantity);
                                        }
                                    })
                                    ->columnSpan([
                                        'sm' => 4,
                                        'md' => 2,
                                        'lg' => 1,
                                    ]),

                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $unitPrice = $get('unit_price') ?? 0;
                                        $unitProfit = $get('unit_profit') ?? 0;
                                        $quantity = $state ?? 1;

                                        $set('total_price', $unitPrice * $quantity);
                                        $set('total_profit', $unitProfit * $quantity);
                                    })
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                TextInput::make('unit_price')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix(config("services.system_params.currency"))
                                    ->live()
                                    ->default(0.00)
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        $quantity = $get('quantity') ?? 1;
                                        $unitPrice = $state ?? 0;

                                        // Get base price from selected product
                                        $productId = $get('product_id');
                                        $basePrice = 0;
                                        if ($productId) {
                                            $product = Product::find($productId);
                                            $basePrice = $product->base_price ?? 0;
                                        }

                                        $set('total_price', $unitPrice * $quantity);

                                        // Calculate profit
                                        $unitProfit = $unitPrice - $basePrice;
                                        $set('unit_profit', $unitProfit);
                                        $set('total_profit', $unitProfit * $quantity);
                                    })
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                TextInput::make('total_price')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->prefix(config("services.system_params.currency"))
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                TextInput::make('unit_profit')
                                    ->label('Unit Profit')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->prefix(config("services.system_params.currency"))
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                TextInput::make('total_profit')
                                    ->label('Total Profit')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->prefix(config("services.system_params.currency"))
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),
                            ])
                            ->columns([
                                'sm' => 6,
                                'md' => 6,
                                'lg' => 6,
                            ])
                            ->relationship()
                            ->defaultItems(1)
                            ->columnSpanFull()
                            ->addActionLabel('Add Item')
                            ->addActionAlignment(Alignment::Start)
                            ->collapsible()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                self::calculateTotals($state, $set, $get);
                            }),

                        // Totals Section - Responsive grid
                        Group::make([
                            TextInput::make('quantity_ordered')
                                ->label('Total Quantity')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->default(0)
                                ->columnSpan([
                                    'sm' => 2,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            TextInput::make('subtotal')
                                ->label('Subtotal')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->prefix(config("services.system_params.currency"))
                                ->default(0.00)
                                ->columnSpan([
                                    'sm' => 2,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            TextInput::make('tax_percent')
                                ->label('Tax (%)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%')
                                ->default(0.00)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    self::calculateTotals($get('orderItems'), $set, $get);
                                })
                                ->columnSpan([
                                    'sm' => 2,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            TextInput::make('tax_amount')
                                ->label('Tax Amount')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->prefix(config("services.system_params.currency"))
                                ->default(0.00)
                                ->columnSpan([
                                    'sm' => 2,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            TextInput::make('discount_percent')
                                ->label('Discount (%)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(100)
                                ->suffix('%')
                                ->default(0.00)
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                    self::calculateTotals($get('orderItems'), $set, $get);
                                })
                                ->columnSpan([
                                    'sm' => 2,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            TextInput::make('discount_amount')
                                ->label('Discount Amount')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->prefix(config("services.system_params.currency"))
                                ->default(0.00)
                                ->columnSpan([
                                    'sm' => 2,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            TextInput::make('total_amount')
                                ->label('Total Amount')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->prefix(config("services.system_params.currency"))
                                ->default(0.00)
                                ->extraAttributes(['class' => 'font-bold'])
                                ->columnSpan([
                                    'sm' => 2,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            TextInput::make('total_profit')
                                ->label('Total Profit')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->prefix(config("services.system_params.currency"))
                                ->default(0.00)
                                ->extraAttributes(['class' => 'font-bold text-green-600'])
                                ->columnSpan([
                                    'sm' => 2,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),

                            TextInput::make('profit_margin')
                                ->label('Profit Margin (%)')
                                ->numeric()
                                ->disabled()
                                ->dehydrated(true)
                                ->suffix('%')
                                ->default(0.00)
                                ->extraAttributes(['class' => 'font-bold text-green-600'])
                                ->columnSpan([
                                    'sm' => 2,
                                    'md' => 1,
                                    'lg' => 1,
                                ]),
                        ])
                            ->columns([
                                'sm' => 4,
                                'md' => 4,
                                'lg' => 4,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns([
                'sm' => 1,
                'md' => 1,
                'lg' => 6,
            ]);
    }

    /**
     * Calculate all totals including tax and discount
     */
    private static function calculateTotals($orderItems, Set $set, Get $get): void
    {
        if (!$orderItems || !is_array($orderItems)) {
            $orderItems = [];
        }

        // Calculate quantity and subtotal
        $quantityOrdered = collect($orderItems)->sum(function ($item) {
            return isset($item['quantity']) && is_numeric($item['quantity'])
                ? (int) $item['quantity']
                : 0;
        });

        $subtotal = collect($orderItems)->sum(function ($item) {
            $unitPrice = isset($item['unit_price']) && is_numeric($item['unit_price'])
                ? (float) $item['unit_price']
                : 0;
            $quantity = isset($item['quantity']) && is_numeric($item['quantity'])
                ? (int) $item['quantity']
                : 0;
            return $unitPrice * $quantity;
        });

        // Get tax and discount percentages
        $taxPercent = (float)($get('tax_percent') ?? 0);
        $discountPercent = (float)($get('discount_percent') ?? 0);

        // Calculate tax amount
        $taxAmount = $subtotal * ($taxPercent / 100);

        // Calculate discount amount
        $discountAmount = $subtotal * ($discountPercent / 100);

        // Calculate total amount (subtotal + tax - discount)
        $totalAmount = $subtotal + $taxAmount - $discountAmount;

        // Ensure total amount is not negative
        $totalAmount = max(0, $totalAmount);

        // Calculate total profit from order items
        $totalProfit = collect($orderItems)->sum(function ($item) {
            $unitPrice = isset($item['unit_price']) && is_numeric($item['unit_price'])
                ? (float) $item['unit_price']
                : 0;
            $quantity = isset($item['quantity']) && is_numeric($item['quantity'])
                ? (int) $item['quantity']
                : 0;

            // Get base price from product
            $productId = $item['product_id'] ?? null;
            $basePrice = 0;
            if ($productId) {
                $product = Product::find($productId);
                $basePrice = $product->base_price ?? 0;
            }

            $unitProfit = $unitPrice - $basePrice;
            return $unitProfit * $quantity;
        });

        // Calculate profit margin percentage
        $profitMargin = $totalAmount > 0 ? ($totalProfit / $totalAmount) * 100 : 0;

        // Set all calculated values
        $set('quantity_ordered', $quantityOrdered);
        $set('subtotal', round($subtotal, 2));
        $set('tax_amount', round($taxAmount, 2));
        $set('discount_amount', round($discountAmount, 2));
        $set('total_amount', round($totalAmount, 2));
        $set('total_profit', round($totalProfit, 2));
        $set('profit_margin', round($profitMargin, 2));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),

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
                    ->sortable()
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Total')
                    ->numeric()
                    ->summarize(Sum::make()->label('Total Amount')->money(config('settings.general_settings.default_currency', 'INR')))
                    ->money(config("services.system_params.currency"))
                    ->sortable(),

                TextColumn::make('total_profit')
                    ->label('Profit')
                    ->numeric()
                    ->money(config("services.system_params.currency"))
                    ->color('success')
                    ->summarize(Sum::make()->label('Total Profit')->money(config('settings.general_settings.default_currency', 'INR')))
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('profit_margin')
                    ->label('Margin %')
                    ->numeric()
                    ->suffix('%')
                    ->color('success')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                    ]),

                BadgeColumn::make('payment_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                    ])
                    ->toggleable(),

                TextColumn::make('order_date')
                    ->dateTime('M j, Y')
                    ->sortable(),

                TextColumn::make('delivered_at')
                    ->label('Delivered')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('user.name')
                    ->label('Created By')
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([

                SelectFilter::make('retailor_id')
                    ->label("Filter by Retailer")
                    ->searchable()
                    ->preload()
                    ->options(Retailor::pluck('name', 'id')),

                SelectFilter::make('brand_id')
                    ->label("Filter by Brand")
                    ->searchable()
                    ->preload()
                    ->options(Brand::pluck('name', 'id')),

                SelectFilter::make('status')
                    ->preload()
                    ->multiple()
                    ->searchable()
                    ->options(OrderStatus::getOptions()),

                SelectFilter::make('payment_status')
                    ->preload()
                    ->searchable()
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                    ]),

                Filter::make('order_date')
                    ->schema([
                        DatePicker::make('from')
                            ->native(false)
                            ->prefixIcon("heroicon-o-calendar")
                            ->label('From Date'),
                        DatePicker::make('until')
                            ->native(false)
                            ->prefixIcon("heroicon-o-calendar")
                            ->label('Until Date'),
                    ])->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('order_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('order_date', '<=', $date),
                            );
                    }),
              
                ],  layout: FiltersLayout::AboveContentCollapsible)->filtersFormColumns(3)
            
            ->recordActions([

                ViewAction::make()
                    ->hiddenLabel()
                    ->size("lg")
                    ->tooltip('View'),

                EditAction::make()
                    ->hiddenLabel()
                    ->size("lg")
                    ->tooltip('Edit'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No orders found')
            ->emptyStateDescription('Create your first order to get started.');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
