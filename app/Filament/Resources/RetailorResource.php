<?php

namespace App\Filament\Resources;

use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\BulkAction;
use App\Filament\Resources\RetailorResource\Pages\ListRetailors;
use App\Filament\Resources\RetailorResource\Pages\CreateRetailor;
use App\Filament\Resources\RetailorResource\Pages\EditRetailor;
use App\Filament\Resources\RetailorResource\Pages\ViewRetailor;
use App\Filament\Resources\RetailorResource\Pages;
use App\Filament\Resources\RetailorResource\RelationManagers;
use App\Models\Retailor;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class RetailorResource extends Resource
{
    use Translatable;

    protected static ?string $model = Retailor::class;
    protected static ?string $modelLabel = 'Retailor';
    protected static string | \UnitEnum | null $navigationGroup = 'Customers Management';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([

                Group::make()->schema([

                    Section::make('Retailor Information')
                        ->columns(2)
                        ->schema([

                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(function (Set $set, $state, $operation) {
                                    if ($operation == 'create') {
                                        $set('slug', Str::slug($state));
                                    }
                                })

                                ->live(onBlur: true)
                                ->label('Name'),

                            TextInput::make('slug')
                                ->required()
                                ->disabledOn(['edit'])
                                ->readOnlyOn('create')
                                ->label('Slug')
                                // ->infotip('Auto Generated')
                                ->maxLength(255)
                                ->label('Slug'),

                            Textarea::make('address')
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->label('Address'),

                            Select::make('city')
                                ->prefixicon('heroicon-o-map-pin')
                                ->preload()
                                ->searchable()
                                ->default('other')
                                ->options([
                                    'navsari'   => 'Navsari',
                                    'surat'     => 'Surat',
                                    'vadodara'  => 'Vadodara',
                                    'valsad'    => 'Valsad',
                                    'other'     => 'Other',
                                ])
                                ->label('City'),

                            TextInput::make('pincode')
                                ->maxLength(255)
                                ->label('Pincode'),

                            TextInput::make('phone')
                                ->tel()
                                ->required()
                                ->prefixIcon('heroicon-o-phone')
                                ->maxLength(255)
                                ->label('Phone'),

                            TextInput::make('email')
                                ->email()
                                ->prefixicon('heroicon-o-envelope')
                                ->maxLength(255)
                                ->label('Email'),

                        ])->columnSpan(2),

                    Section::make('Additional Information')
                        ->schema([

                            FileUpload::make('logo')
                                // ->image()
                                ->imageEditor()
                                ->directory('retailor')
                                ->columnSpanFull()
                                ->label('Logo'),

                            Textarea::make('description')
                                ->label('Description'),

                            Textarea::make('additional_info')
                                ->maxLength(1000)
                                ->label('Additional Information'),

                        ])->columnSpan(1)->columns(2),

                ])->columnSpan(2),

                Group::make()->schema([

                    Section::make('Status')
                        ->description("Retailor Management")
                        ->schema([

                            Select::make('authorized_person')
                                ->options(User::role('retailor')->pluck('name', 'id'))
                                ->label('Authorized Person'),

                            Radio::make('status')
                                ->inline()
                                ->inlineLabel(false)
                                ->default('verified')
                                ->options([
                                    'pending' => 'Pending',
                                    'verified' => 'Verified',
                                    'rejected' => 'Rejected',
                                ])
                                ->label('Status'),

                            Toggle::make('is_active')
                                ->required()
                                ->label('Is Active'),

                        ])->columnSpan(2),

                ])->columnSpan(1),


            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo')
                    ->label('')
                    ->size(50)
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?name='),

                TextColumn::make('name')
                    ->searchable(['name', 'city'])
                    ->description(fn($record)=> "City: ". ucfirst($record->city) ?? "Na")
                    ->sortable()
                    ->label('Retailor Name')
                    ->limit(25),

                TextColumn::make('phone')
                    ->searchable(["phone", "email"])
                    ->label("Contact")
                    ->description(fn($record)=> $record->email ?? "Na")
                    ->icon('heroicon-o-phone'),


                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state)=>ucfirst($state))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ])
                    ->label('Status'),

                ToggleColumn::make('is_active')
                    ->label('Active'),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created'),
            ])
            ->filters([
                SelectFilter::make('city')
                    ->options([
                        'navsari' => 'Navsari',
                        'surat' => 'Surat',
                        'vadodara' => 'Vadodara',
                        'valsad' => 'Valsad',
                        'other' => 'Other',
                    ])
                    ->searchable()
                    ->preload()
                    ->label('Filter by City'),

                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->searchable()
                    ->preload()
                    ->label('Filter by Status'),

                TernaryFilter::make('is_active')
                    ->label('Active Status'),

                Filter::make('created_date_range')
                    ->schema([
                        DatePicker::make('created_from')
                            ->label('Created From'),
                        DatePicker::make('created_until')
                            ->label('Created Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->label('Created Date Range'),

                Filter::make('pincode_search')
                    ->schema([
                        TextInput::make('pincode')
                            ->label('Pincode')
                            ->placeholder('Enter pincode to search'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['pincode'],
                                fn(Builder $query, $pincode): Builder => $query->where('pincode', 'like', "%{$pincode}%"),
                            );
                    })
                    ->label('Search by Pincode'),
            ])
            ->recordActions([
                ViewAction::make()->label("")->tooltip("View")->size("lg"),
                EditAction::make()->label("")->tooltip("Edit")->size("lg"),
                DeleteAction::make()->label("")->tooltip("Delete")->size("lg"),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    BulkAction::make('activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->label('Activate Selected')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => true]);
                            });
                        })
                        ->requiresConfirmation(),

                    BulkAction::make('deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->label('Deactivate Selected')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['is_active' => false]);
                            });
                        })
                        ->requiresConfirmation(),

                    BulkAction::make('mark_verified')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->label('Mark as Verified')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'verified']);
                            });
                        })
                        ->requiresConfirmation(),

                    BulkAction::make('mark_pending')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->label('Mark as Pending')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'pending']);
                            });
                        })
                        ->requiresConfirmation(),

                    BulkAction::make('mark_rejected')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->label('Mark as Rejected')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'rejected']);
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
            'index' => ListRetailors::route('/'),
            'create' => CreateRetailor::route('/create'),
            'edit' => EditRetailor::route('/{record}/edit'),
            'view' => ViewRetailor::route('/{record}'),
        ];
    }
}
