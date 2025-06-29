<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
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
                            ->afterStateUpdated(function(Set $set, $state, Model $record) {
                                if($record){
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->label('Name'),

                        Forms\Components\Select::make('brand_id')
                            ->relationship('brand', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->label('Brand'),

                        Forms\Components\Select::make('category_id')
                            ->relationship('category', 'name')
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
                            ->placeholder('Enter Product Description : max 255 characters')
                            ->label('Product Description'),

                        Forms\Components\TextInput::make('quantity')
                            ->placeholder('Enter approximate quantity: 100 gm')
                            ->numeric()
                            ->label('Quantity'),

                        Forms\Components\TextInput::make('base_price')
                            ->placeholder('E.g: 1000')
                            ->numeric()
                            ->label('Base Price'),

                        Forms\Components\TextInput::make('sell_price')
                            ->numeric()
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('brand_id')
                    ->searchable()
                    ->label('Brand ID'),
                Tables\Columns\TextColumn::make('category_id')
                    ->searchable()
                    ->label('Category ID'),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->label('Description'),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('base_price')
                    ->searchable()
                    ->label('Base Price'),
                Tables\Columns\TextColumn::make('sell_price')
                    ->searchable()
                    ->label('Sell Price'),
                Tables\Columns\TextColumn::make('quantity')
                    ->searchable()
                    ->label('Quantity'),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('Is Featured'),
                Tables\Columns\ToggleColumn::make('is_popular')
                    ->label('Is Popular'),
                Tables\Columns\ToggleColumn::make('is_new')
                    ->label('Is New'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Is Active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated At'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Is Featured'),
                Tables\Filters\TernaryFilter::make('is_popular')
                    ->label('Is Popular'),
                Tables\Filters\TernaryFilter::make('is_new')
                    ->label('Is New'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Is Active'),
            ]);
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
        ];
    }
}
