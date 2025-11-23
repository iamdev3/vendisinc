<?php

namespace App\Filament\Resources\BrandResource\RelationManagers;

use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use App\Models\Category;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('product_information'))
                    ->description(__('basic_product_info'))
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
                            ->required()
                            ->placeholder(__('enter_product_name'))
                            ->maxLength(255)
                            ->label(__('name')),

                        Select::make('category_id')
                            ->options(Category::all()->pluck('name', 'id'))
                            ->required()
                            ->preload()
                            ->searchable()
                            ->label(__('category')),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->placeholder(__('eg_brand_name'))
                            ->helperText(__('add_unique_slug'))
                            ->maxLength(255)
                            ->label(__('slug')),

                        Textarea::make('description')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder(__('enter_description'))
                            ->label(__('description')),

                        TextInput::make('quantity')
                            ->placeholder(__('enter_quantity'))
                            ->numeric()
                            ->label(__('quantity')),

                        TextInput::make('base_price')
                            ->placeholder(__('eg_1000'))
                            ->prefix(config('services.system_params.currency'))
                            ->label(__('base_price')),

                        TextInput::make('sell_price')
                            ->numeric()
                            ->prefix(config('services.system_params.currency'))
                            ->placeholder(__('eg_1500'))
                            ->label(__('sell_price')),

                        FileUpload::make('image')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(1024 * 2)
                            ->placeholder(__('upload_image'))
                            ->image()
                            ->label(__('image')),

                    ])->columnSpan(2),

                Section::make(__('product_management'))
                    ->description(__('product_management_info'))
                    ->schema([

                        Toggle::make('is_featured')
                            ->required()
                            ->label(__('is_featured')),
                        Toggle::make('is_popular')
                            ->required()
                            ->label(__('is_popular')),
                        Toggle::make('is_new')
                            ->required()
                            ->label(__('is_new')),
                        Toggle::make('is_active')
                            ->required()
                            ->label(__('is_active')),
                        Textarea::make('additional_info')
                            ->columnSpanFull()
                            ->label(__('additional_info')),

                    ])->columnSpan(1),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('product_name'))
                    ->limit(30),

                TextColumn::make('category.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('category')),

                TextColumn::make('sell_price')
                    ->money('INR')
                    ->sortable()
                    ->label(__('price')),

                TextColumn::make('quantity')
                    ->searchable()
                    ->sortable()
                    ->label(__('quantity')),

                ToggleColumn::make('is_active')
                    ->label(__('is_active')),

                ToggleColumn::make('is_featured')
                    ->label(__('is_featured')),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label(__('created_at')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()->label("")->tooltip(__("Edit"))->size("lg"),
                DissociateAction::make()->label("")->tooltip(__("Dissociate"))->size("lg"),
                DeleteAction::make()->label("")->tooltip(__("Delete"))->size("lg"),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
