<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Enums\OrderStatus;
use App\Models\Brand;
use App\Models\Retailor;
use Filament\Forms\Set;
use Filament\Support\Enums\Alignment;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationGroup = "Orders";
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Primary Order Information - Full width on mobile, left side on desktop
                Section::make('Order Information')
                    ->schema([
                        Forms\Components\Select::make('retailer_id')
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

                        Forms\Components\TextInput::make('order_number')
                            ->required()
                            ->disabledOn("edit")
                            ->visibleOn("edit")
                            ->maxLength(255)
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabledOn("edit")
                            ->visibleOn("edit")
                            ->required()
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Forms\Components\Select::make('brand_id')
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

                        Forms\Components\DateTimePicker::make('order_date')
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

                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->disabledOn("create")
                            ->label("Delivered Date")
                            ->native(false)
                            ->prefixicon('heroicon-o-calendar')
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 1,
                                'lg' => 1,
                            ]),

                        Forms\Components\Textarea::make('notes')
                            ->label("Note for Retailor")
                            ->rows(3),
                        // ->columnSpanFull(),

                        Forms\Components\Textarea::make('internal_notes')
                            ->label("Internal Notes")
                            ->rows(3),
                        // ->columnSpanFull(),

                        // Status fields - Stack on mobile, inline on desktop
                        // Group::make([
                        Forms\Components\Radio::make('status')
                            ->options(OrderStatus::getOptions())
                            ->inline()
                            ->inlinelabel(false)
                            ->columnSpanFull()
                            ->default(OrderStatus::CONFIRMED)
                            ->required(),
                        // ])->columnSpanFull(),

                        // Group::make([
                        Forms\Components\Radio::make('payment_status')
                            ->options([
                                'pending' => "Pending",
                                'paid'    => "Paid",
                            ])
                            ->default("pending")
                            ->inline()
                            ->inlineLabel(false)
                            ->required(),
                        // ])->columnSpanFull(),

                        Forms\Components\Select::make('payment_method')
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
                        Forms\Components\TextInput::make('customer_information.customer_name')
                            ->label("Customer Name")
                            ->prefixicon("heroicon-o-user")
                            ->disabledOn("edit")
                            ->maxLength(255)
                            ->columnSpan([
                                'sm' => 2,
                                'md' => 2,
                                'lg' => 2,
                            ]),

                        Forms\Components\TextInput::make('customer_information.customer_phone')
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

                        Forms\Components\TextInput::make('customer_information.customer_email')
                            ->label("Customer Email")
                            ->email()
                            ->disabledOn("edit")
                            ->prefixicon('heroicon-o-envelope')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('customer_information.customer_address')
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
                        Forms\Components\Repeater::make('orderItems')
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Product')
                                    ->options(\App\Models\Product::all()->pluck('name', 'id'))
                                    ->required()
                                    ->preload()
                                    ->searchable()
                                    ->live()
                                    ->afterStateUpdated(function ($state, $set, $get) {
                                        if ($state) {
                                            $product = \App\Models\Product::find($state);
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

                                Forms\Components\TextInput::make('quantity')
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

                                Forms\Components\TextInput::make('unit_price')
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
                                            $product = \App\Models\Product::find($productId);
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

                                Forms\Components\TextInput::make('total_price')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(true)
                                    ->prefix(config("services.system_params.currency"))
                                    ->columnSpan([
                                        'sm' => 2,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\TextInput::make('unit_profit')
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

                                Forms\Components\TextInput::make('total_profit')
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
                            Forms\Components\TextInput::make('quantity_ordered')
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

                            Forms\Components\TextInput::make('subtotal')
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

                            Forms\Components\TextInput::make('tax_percent')
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

                            Forms\Components\TextInput::make('tax_amount')
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

                            Forms\Components\TextInput::make('discount_percent')
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

                            Forms\Components\TextInput::make('discount_amount')
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

                            Forms\Components\TextInput::make('total_amount')
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

                            Forms\Components\TextInput::make('total_profit')
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

                            Forms\Components\TextInput::make('profit_margin')
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
                $product = \App\Models\Product::find($productId);
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
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brand')
                    ->icon('heroicon-s-arrow-top-right-on-square')
                    ->url(fn($record): string => BrandResource::getUrl("edit", ["record" => $record->brand_id]))
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('retailor.name')
                    ->label('Retailer')
                    ->icon('heroicon-s-arrow-top-right-on-square')
                    ->url(fn($record): string => RetailorResource::getUrl("edit", ["record" => $record->retailor]))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->numeric()
                    ->money(config("services.system_params.currency"))
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_profit')
                    ->label('Profit')
                    ->numeric()
                    ->money(config("services.system_params.currency"))
                    ->color('success')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('profit_margin')
                    ->label('Margin %')
                    ->numeric()
                    ->suffix('%')
                    ->color('success')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'danger' => 'cancelled',
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'primary' => 'shipped',
                        'success' => 'delivered',
                    ]),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                    ])
                    ->toggleable(),

                Tables\Columns\TextColumn::make('order_date')
                    ->dateTime('M j, Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('delivered_at')
                    ->label('Delivered')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created By')
                    ->toggleable()
                    ->toggledHiddenByDefault(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([

                Tables\Filters\SelectFilter::make('status')
                    ->preload()
                    ->searchable()
                    ->options(OrderStatus::getOptions()),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->preload()
                    ->searchable()
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                    ]),

                Tables\Filters\Filter::make('order_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date'),
                    ])
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

                Tables\Filters\SelectFilter::make('retailor_id')
                    ->label("Filter by Retaioler")
                    ->searchable()
                    ->preload()
                    ->options(Retailor::pluck('name', 'id')),

                Tables\Filters\SelectFilter::make('brand_id')
                    ->label("Filter by Brand")
                    ->searchable()
                    ->preload()
                    ->options(Brand::pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hiddenLabel()
                    ->tooltip('View'),
                Tables\Actions\EditAction::make()
                    ->hiddenLabel()
                    ->tooltip('Edit'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
