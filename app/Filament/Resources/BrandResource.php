<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\BrandResource\Pages\ListBrands;
use App\Filament\Resources\BrandResource\Pages\CreateBrand;
use App\Filament\Resources\BrandResource\Pages\EditBrand;
use App\Filament\Resources\BrandResource\Pages\ViewBrand;
use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Closure;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class BrandResource extends Resource
{
    use Translatable;

    protected static ?string $model = Brand::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Brand Management';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Brand Information')
                    ->description("Basic Brand Related Information")
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function($operation, Get $get, Set $set, $state) {
                                if($operation == 'create'){
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->label('Name'),

                        TextInput::make('phone')
                            ->tel()
                            ->prefixIcon('heroicon-o-phone')
                            ->maxLength(255)
                            ->label('Phone'),

                        TextInput::make('email')
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->maxLength(255)
                            ->label('Email'),

                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->placeholder("eg:brand-name")
                            ->helperText("Add a unique slug for the brand (eg: brand-name)")
                            ->maxLength(255)
                            ->label('Slug')
                            ->dehydrated(true),

                        Textarea::make('address')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->label('Address'),

                        TextInput::make('city')
                            ->maxLength(255)
                            ->label('City'),

                        TextInput::make('pincode')
                            ->maxLength(255)
                            ->label('Pincode'),

                        TextInput::make('website')
                            ->maxLength(255)
                            ->label('Website'),

                    ])->columnSpan(2),

                Section::make('Additional Information')
                    ->schema([

                        FileUpload::make('logo')
                            ->image()
                            ->imageEditor()
                            ->directory('brands')
                            ->uploadingMessage('Uploading brand logo...')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(1024 * 2)
                            ->placeholder("Upload a logo for the brand")
                            ->helperText("Accepted file types: jpeg, png, webp, Max Size : 2MB")
                            ->label('Logo'),

                        Textarea::make('description')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->rows(4)
                            ->maxLength(500)
                            ->placeholder("Enter a description for the brand, Max length 500 characters")
                            ->label('Description'),

                        Textarea::make('additional_info')
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->label('Additional Information'),

                        Toggle::make('is_active')
                            ->required()
                            ->default(true)
                            ->label('Is Active'),

                    ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                ImageColumn::make('logo')
                    ->grow(false)
                    ->label("")
                    ->defaultImageUrl('/images/brand_placeholder.png')
                    ->circular(),

                TextColumn::make('name')
                    ->searchable(['name', 'phone'])
                    ->grow(false)
                    ->description(fn(Brand $record) => $record->phone ?? 'N/A')
                    ->label('Name'),

                TextColumn::make('city')
                    ->searchable()
                    ->grow()
                    ->alignCenter()
                    ->label('City'),

                ToggleColumn::make('is_active')
                    // ->grow()
                    ->label('Is Active'),

                TextColumn::make('created_at')
                    ->date("d-m-Y")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated At'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Is Active'),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()->label("")->tooltip("Delete Brand")->size("md"),
            ])
            ->recordActions([
                ViewAction::make()->label("")->tooltip("View Brand")->size("lg"),
                EditAction::make()->label("")->tooltip("Edit Brand")->size("lg"),
                // Tables\Actions\DeleteAction::make()->label("")->tooltip("Delete Brand")->size("md"),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
            'view' => ViewBrand::route('/{record}'),
        ];
    }

}