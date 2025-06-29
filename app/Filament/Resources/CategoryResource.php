<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
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
use TangoDevIt\FilamentEmojiPicker\EmojiPickerAction;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationGroup = 'Brand Management';
    protected static? Int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->description("Basic Category Related Information")
                    ->columns(2)
                    ->schema([

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->reactive()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function(Set $set, $state, ?Model $record) {
                                if (!$record) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->label('Name'),

                        Forms\Components\Select::make('parent_id')
                            ->options(Category::where('parent_id', null)->pluck('name', 'id'))
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->label('Parent ID'),

                        Forms\Components\TextInput::make('icon')
                            ->maxLength(255)
                            ->suffixAction(EmojiPickerAction::make('emoji-title'))
                            ->regex('/^(?:[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F018}-\x{1F270}]|[\x{238C}]|[\x{2764}]|[\x{FE0F}]|[\x{200D}])+$/u')
                            ->validationMessages([
                                'regex' => 'Please use the emoji picker to select a valid emoji.',
                            ])
                            ->helperText('Use the 😀 button to pick an emoji')
                            ->label('Icon'),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->label('Slug'),

                        Forms\Components\Textarea::make('description')
                            ->maxLength(255)
                            ->placeholder('Enter Description about 255 characters')
                            ->label('Description'),

                        Forms\Components\Textarea::make('additional_info')
                            ->maxLength(150)
                            ->placeholder('Enter Additional Information about 150 characters')
                            ->label('Additional Information'),

                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->label('Is Active'),

                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->label('Slug'),
                Tables\Columns\TextColumn::make('icon')
                    ->searchable()
                    ->label('Icon'),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->label('Description'),
                Tables\Columns\TextColumn::make('parent_id')
                    ->searchable()
                    ->label('Parent ID'),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
