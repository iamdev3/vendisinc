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
                                ->columnSpan(2)
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
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->label('Slug'),
                Tables\Columns\TextColumn::make('logo')
                    ->searchable()
                    ->label('Logo'),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->label('Address'),
                Tables\Columns\TextColumn::make('city')
                    ->searchable()
                    ->label('City'),
                Tables\Columns\TextColumn::make('pincode')
                    ->searchable()
                    ->label('Pincode'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->label('Phone'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Is Active'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status'),
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

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'rejected' => 'Rejected',
                    ])
                    ->label('Status'),
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
            'index' => Pages\ListRetailors::route('/'),
            'create' => Pages\CreateRetailor::route('/create'),
            'edit' => Pages\EditRetailor::route('/{record}/edit'),
        ];
    }
}
