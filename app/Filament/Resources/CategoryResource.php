<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use TangoDevIt\FilamentEmojiPicker\EmojiPickerAction;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class CategoryResource extends Resource
{   
    use Translatable;
    
    protected static ?string $model = Category::class;
    protected static string | \UnitEnum | null $navigationGroup = 'Brand Management';
    protected static? Int $navigationSort = 3;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category Information')
                    ->description("Basic Category Related Information")
                    ->columns(2)
                    ->schema([

                        TextInput::make('name')
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

                        Select::make('parent_id')
                            ->options(Category::where('parent_id', null)->pluck('name', 'id'))
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->label('Parent ID'),

                        // TextInput::make('icon')
                        //     ->maxLength(255)
                        //     ->suffixAction(EmojiPickerAction::make('emoji-title'))
                        //     ->regex('/^(?:[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]|[\x{1F900}-\x{1F9FF}]|[\x{1F018}-\x{1F270}]|[\x{238C}]|[\x{2764}]|[\x{FE0F}]|[\x{200D}])+$/u')
                        //     ->validationMessages([
                        //         'regex' => 'Please use the emoji picker to select a valid emoji.',
                        //     ])
                        //     ->helperText('Use the 😀 button to pick an emoji')
                        //     ->label('Icon'),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->disabledOn("edit")
                            ->dehydrated(true)
                            ->label('Slug'),

                        Textarea::make('description')
                            ->maxLength(255)
                            ->placeholder('Enter Description about 255 characters')
                            ->label('Description'),                     

                        Toggle::make('is_active')
                            ->required()
                            ->label('Is Active'),

                    ])->columns(2)->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                
                TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),

                TextColumn::make('slug')
                    ->searchable()
                    ->label('Slug'),

                TextColumn::make('icon')
                    ->searchable()
                    ->label('Icon'),
              
                TextColumn::make('parent.name')
                    ->searchable()
                    ->label('Parent Category'),

                ToggleColumn::make('is_active')
                    ->label('Is Active'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created At'),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Updated At'),
            ])
            ->recordActions([

                ViewAction::make()->label("")->tooltip(__('view'))->size("lg"),
                EditAction::make()->label("")->tooltip(__('edit'))->size("lg"),
                DeleteAction::make()->label("")->tooltip(__('delete'))->size("lg"),

            ])
            ->filters([
                TernaryFilter::make('is_active')
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
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }
}
