<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{

    protected static ?string $navigationGroup = 'Brand Management';
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Product Information')
                    ->description("Basic Product Related Information")
                    ->columns(2)
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('Enter Product Name')
                            ->maxLength(255)
                            ->live(onBlur: true)
                            // ->afterStateUpdated(function(Set $set, $state, Model $record) {
                            //     if($record){
                            //         $set('slug', Str::slug($state));
                            //     }
                            // })
                            ->label('Name'),

                        Forms\Components\Select::make('brand_id')
                            // ->relationship('brand', 'name')
                            ->options(Brand::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Brand'),

                        Forms\Components\Select::make('category_id')
                            // ->relationship('category', 'name')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->required()
                            ->preload()
                            ->searchable()
                            ->label('Category'),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->placeholder("eg:brand-name")
                            ->helperText("Add a unique slug for the brand (eg: brand-name)")
                            ->maxLength(255)
                            ->label('Slug'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('Enter Product Description : max 255 characters')
                            ->label('Product Description'),

                        Forms\Components\TextInput::make('quantity')
                            ->placeholder('Enter approximate quantity: 100 gm')
                            ->numeric()
                            ->label('Quantity'),

                        Forms\Components\TextInput::make('base_price')
                            ->placeholder('E.g: 1000')
                            ->prefix(config('services.system_params.currency'))
                            ->label('Base Price'),

                        Forms\Components\TextInput::make('sell_price')
                            ->numeric()
                            ->prefix(config('services.system_params.currency'))
                            ->placeholder('E.g: 1500')
                            ->label('Sell Price'),

                        Forms\Components\FileUpload::make('image')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(1024 * 2)
                            ->placeholder('Upload Product Image, format : jpeg, png, webp. Max Size : 2 MB')
                            ->image()
                            ->label('Image'),

                    ])->columnSpan(2),

                Forms\Components\Section::make('Product Management')
                    ->description("Product Management & Editional Information")
                    ->schema([

                        Forms\Components\Toggle::make('is_featured')
                            ->required()
                            ->label('Is Featured'),
                        Forms\Components\Toggle::make('is_popular')
                            ->required()
                            ->label('Is Popular'),
                        Forms\Components\Toggle::make('is_new')
                            ->required()
                            ->label('Is New'),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->label('Is Active'),
                        Forms\Components\Textarea::make('additional_info')
                            ->columnSpanFull()
                            ->label('Additional Information'),

                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\ImageColumn::make('image')
                //     ->label('Image')
                //     ->size(50)
                //     ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Product Name')
                    ->limit(30),

                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable()
                    ->label('Brand'),

                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->label('Category'),

                Tables\Columns\TextColumn::make('sell_price')
                    ->money('INR')
                    ->sortable()
                    ->label('Price'),

                Tables\Columns\TextColumn::make('quantity')
                    ->searchable()
                    ->sortable()
                    ->label('Quantity'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Featured'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Brand'),

                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Category'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Featured Status'),

                Tables\Filters\TernaryFilter::make('is_popular')
                    ->label('Popular Status'),

                Tables\Filters\TernaryFilter::make('is_new')
                    ->label('New Status'),

                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->numeric()
                            ->placeholder('Min Price')
                            ->label('Minimum Price'),
                        Forms\Components\TextInput::make('max_price')
                            ->numeric()
                            ->placeholder('Max Price')
                            ->label('Maximum Price'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_price'],
                                fn(Builder $query, $minPrice): Builder => $query->where('sell_price', '>=', $minPrice),
                            )
                            ->when(
                                $data['max_price'],
                                fn(Builder $query, $maxPrice): Builder => $query->where('sell_price', '<=', $maxPrice),
                            );
                    })
                    ->label('Price Range'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label("")->tooltip("View")->size("lg"),
                Tables\Actions\EditAction::make()->label("")->tooltip("Edit")->size("lg"),
                Tables\Actions\DeleteAction::make()->label("")->tooltip("Delete")->size("lg"),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->label('Activate Selected')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => true]);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->label('Deactivate Selected')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => false]);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_featured')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->label('Mark as Featured')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_featured' => true]);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('unmark_featured')
                        ->icon('heroicon-o-star')
                        ->color('gray')
                        ->label('Remove Featured')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_featured' => false]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'view' => Pages\ViewProduct::route('/{record}'),
        ];
    }
}
