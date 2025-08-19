<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Closure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;
    protected static ?string $navigationGroup = 'Brand Management';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Brand Information')
                    ->description("Basic Brand Related Information")
                    ->columns(2)
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function($operation, Get $get, Set $set, $state) {
                                if($operation == 'create'){
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->label('Name'),

                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->prefixIcon('heroicon-o-phone')
                            ->maxLength(255)
                            ->label('Phone'),

                        Forms\Components\TextInput::make('email')
                            ->prefixIcon('heroicon-o-envelope')
                            ->email()
                            ->maxLength(255)
                            ->label('Email'),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabledOn('edit')
                            ->placeholder("eg:brand-name")
                            ->helperText("Add a unique slug for the brand (eg: brand-name)")
                            ->maxLength(255)
                            ->label('Slug'),

                        Forms\Components\Textarea::make('address')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->label('Address'),

                        Forms\Components\TextInput::make('city')
                            ->maxLength(255)
                            ->label('City'),

                        Forms\Components\TextInput::make('pincode')
                            ->maxLength(255)
                            ->label('Pincode'),

                        Forms\Components\TextInput::make('website')
                            ->maxLength(255)
                            ->label('Website'),

                    ])->columnSpan(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([

                        Forms\Components\FileUpload::make('logo')
                            ->image()
                            ->imageEditor()
                            ->directory('brands')
                            ->uploadingMessage('Uploading brand logo...')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(1024 * 2)
                            ->placeholder("Upload a logo for the brand")
                            ->helperText("Accepted file types: jpeg, png, webp, Max Size : 2MB")
                            ->label('Logo'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->rows(4)
                            ->maxLength(500)
                            ->placeholder("Enter a description for the brand, Max length 500 characters")
                            ->label('Description'),

                        Forms\Components\Textarea::make('additional_info')
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->label('Additional Information'),

                        Forms\Components\Toggle::make('is_active')
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

                Tables\Columns\ImageColumn::make('logo')
                    ->grow(false)
                    ->label("")
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(['name', 'phone'])
                    ->grow(false)
                    ->description(fn(Brand $record) => $record->phone ?? 'N/A')
                    ->label('Name'),

                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->grow()
                    ->alignCenter()
                    ->label('City'),

                Tables\Columns\ToggleColumn::make('is_active')
                    // ->grow()
                    ->label('Is Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->date("d-m-Y")
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
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Is Active'),
            ])
            ->bulkActions([
            Tables\Actions\DeleteBulkAction::make()->label("")->tooltip("Delete Brand")->size("md"),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label("")->tooltip("View Brand")->size("md"),
                Tables\Actions\EditAction::make()->label("")->tooltip("Edit Brand")->size("md"),
                // Tables\Actions\DeleteAction::make()->label("")->tooltip("Delete Brand")->size("md"),
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
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
            'view' => Pages\ViewBrand::route('/{record}'),
        ];
    }
}
