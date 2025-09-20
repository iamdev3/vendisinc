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
use Filament\Resources\Concerns\Translatable;


class ProductResource extends Resource
{
    use Translatable;

    protected static ?string $navigationGroup = 'Brand Management';
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('product_information'))
                    ->description(__('basic_product_info'))
                    ->columns(2)
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder(__('enter_product_name'))
                            ->maxLength(255)
                            ->live(onBlur: true)
                            // ->afterStateUpdated(function(Set $set, $state, Model $record) {
                            //     if($record){
                            //         $set('slug', Str::slug($state));
                            //     }
                            // })
                            ->label(__('name')),

                        Forms\Components\Select::make('brand_id')
                            // ->relationship('brand', 'name')
                            ->options(Brand::all()->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label(__('brand')),

                        Forms\Components\Select::make('category_id')
                            // ->relationship('category', 'name')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->required()
                            ->preload()
                            ->searchable()
                            ->label(__('category')),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->placeholder(__('eg_brand_name'))
                            ->helperText(__('add_unique_slug'))
                            ->maxLength(255)
                            ->label(__('slug')),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder(__('enter_description'))
                            ->label(__('description')),

                        Forms\Components\TextInput::make('quantity')
                            ->placeholder(__('enter_quantity'))
                            ->numeric()
                            ->label(__('quantity')),

                        Forms\Components\TextInput::make('base_price')
                            ->placeholder(__('eg_1000'))
                            ->prefix(config('services.system_params.currency'))
                            ->label(__('base_price')),

                        Forms\Components\TextInput::make('sell_price')
                            ->numeric()
                            ->prefix(config('services.system_params.currency'))
                            ->placeholder(__('eg_1500'))
                            ->label(__('sell_price')),

                        Forms\Components\FileUpload::make('image')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(1024 * 2)
                            ->placeholder(__('upload_image'))
                            ->image()
                            ->label(__('image')),

                    ])->columnSpan(2),

                Forms\Components\Section::make(__('product_management'))
                    ->description(__('product_management_info'))
                    ->schema([

                        Forms\Components\Toggle::make('is_featured')
                            ->required()
                            ->label(__('is_featured')),
                        Forms\Components\Toggle::make('is_popular')
                            ->required()
                            ->label(__('is_popular')),
                        Forms\Components\Toggle::make('is_new')
                            ->required()
                            ->label(__('is_new')),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->label(__('is_active')),
                        Forms\Components\Textarea::make('additional_info')
                            ->columnSpanFull()
                            ->label(__('additional_info')),

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
                    ->label(__('product_name'))
                    ->limit(30),

                Tables\Columns\TextColumn::make('brand.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('brand')),

                Tables\Columns\TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('category')),

                Tables\Columns\TextColumn::make('sell_price')
                    ->money('INR')
                    ->sortable()
                    ->label(__('price')),

                Tables\Columns\TextColumn::make('quantity')
                    ->searchable()
                    ->sortable()
                    ->label(__('quantity')),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('is_active')),

                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label(__('is_featured')),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('created_at')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('filter_by_brand')),

                Tables\Filters\SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('filter_by_category')),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('is_active')),

                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label(__('is_featured')),

                Tables\Filters\TernaryFilter::make('is_popular')
                    ->label(__('is_popular')),

                Tables\Filters\TernaryFilter::make('is_new')
                    ->label(__('is_new')),

                Tables\Filters\Filter::make('price_range')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->numeric()
                            ->placeholder(__('min_price'))
                            ->label(__('min_price')),
                        Forms\Components\TextInput::make('max_price')
                            ->numeric()
                            ->placeholder(__('max_price'))
                            ->label(__('max_price')),
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
                    ->label(__('price_range')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label("")->tooltip(__('view'))->size("lg"),
                Tables\Actions\EditAction::make()->label("")->tooltip(__('edit'))->size("lg"),
                Tables\Actions\DeleteAction::make()->label("")->tooltip(__('delete'))->size("lg"),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->label(__('activate_selected'))
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => true]);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->label(__('deactivate_selected'))
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => false]);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_featured')
                        ->icon('heroicon-o-star')
                        ->color('warning')
                        ->label(__('mark_featured'))
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_featured' => true]);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('unmark_featured')
                        ->icon('heroicon-o-star')
                        ->color('gray')
                        ->label(__('remove_featured'))
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
