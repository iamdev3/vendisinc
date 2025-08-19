<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RetailorResource\Pages;
use App\Filament\Resources\RetailorResource\RelationManagers;
use App\Models\Retailor;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Resources\Concerns\Translatable;

class RetailorResource extends Resource
{
    use Translatable;

    protected static ?string $model = Retailor::class;
    protected static ?string $modelLabel = 'Retailor';
    protected static ?string $navigationGroup = 'Customers Management';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Group::make()->schema([

                    Forms\Components\Section::make('Retailor Information')
                        ->columns(2)
                        ->schema([

                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(function (Set $set, $state, $operation) {
                                    if ($operation == 'create') {
                                        $set('slug', Str::slug($state));
                                    }
                                })

                                ->live(onBlur: true)
                                ->label('Name'),

                            Forms\Components\TextInput::make('slug')
                                ->required()
                                ->disabledOn(['edit'])
                                ->readOnlyOn('create')
                                ->label('Slug')
                                // ->infotip('Auto Generated')
                                ->maxLength(255)
                                ->label('Slug'),

                            Forms\Components\Textarea::make('address')
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->label('Address'),

                            Forms\Components\Select::make('city')
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

                            Forms\Components\TextInput::make('pincode')
                                ->maxLength(255)
                                ->label('Pincode'),

                            Forms\Components\TextInput::make('phone')
                                ->tel()
                                ->required()
                                ->prefixIcon('heroicon-o-phone')
                                ->maxLength(255)
                                ->label('Phone'),

                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->prefixicon('heroicon-o-envelope')
                                ->maxLength(255)
                                ->label('Email'),

                        ])->columnSpan(2),

                    Forms\Components\Section::make('Additional Information')
                        ->schema([

                            Forms\Components\FileUpload::make('logo')
                                // ->image()
                                ->imageEditor()
                                ->directory('retailor')
                                ->columnSpanFull()
                                ->label('Logo'),

                            Forms\Components\Textarea::make('description')
                                ->label('Description'),

                            Forms\Components\Textarea::make('additional_info')
                                ->maxLength(1000)
                                ->label('Additional Information'),

                        ])->columnSpan(1)->columns(2),

                ])->columnSpan(2),

                Group::make()->schema([

                    Forms\Components\Section::make('Status')
                        ->description("Retailor Management")
                        ->schema([

                            Forms\Components\Select::make('authorized_person')
                                ->options(User::role('retailor')->pluck('name', 'id'))
                                ->label('Authorized Person'),

                            Forms\Components\Radio::make('status')
                                ->inline()
                                ->inlineLabel(false)
                                ->default('verified')
                                ->options([
                                    'pending' => 'Pending',
                                    'verified' => 'Verified',
                                    'rejected' => 'Rejected',
                                ])
                                ->label('Status'),

                            Forms\Components\Toggle::make('is_active')
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
                Tables\Columns\ImageColumn::make('logo')
                    ->label('')
                    ->size(50)
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?name='),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(['name', 'city'])
                    ->description(fn($record)=> "City: ". ucfirst($record->city) ?? "Na")
                    ->sortable()
                    ->label('Retailor Name')
                    ->limit(25),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable(["phone", "email"])
                    ->label("Contact")
                    ->description(fn($record)=> $record->email ?? "Na")
                    ->icon('heroicon-o-phone'),


                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn($state)=>ucfirst($state))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'rejected',
                    ])
                    ->label('Status'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Created'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('city')
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

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->searchable()
                    ->preload()
                    ->label('Filter by Status'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active Status'),

                Tables\Filters\Filter::make('created_date_range')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Created From'),
                        Forms\Components\DatePicker::make('created_until')
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

                Tables\Filters\Filter::make('pincode_search')
                    ->form([
                        Forms\Components\TextInput::make('pincode')
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

                    Tables\Actions\BulkAction::make('mark_verified')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->label('Mark as Verified')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'verified']);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_pending')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->label('Mark as Pending')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => 'pending']);
                            });
                        })
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_rejected')
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
            'index' => Pages\ListRetailors::route('/'),
            'create' => Pages\CreateRetailor::route('/create'),
            'edit' => Pages\EditRetailor::route('/{record}/edit'),
            'view' => Pages\ViewRetailor::route('/{record}'),
        ];
    }
}
