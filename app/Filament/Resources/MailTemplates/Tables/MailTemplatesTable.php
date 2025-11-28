<?php

namespace App\Filament\Resources\MailTemplates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MailTemplatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label("Email Purpose")
                    ->searchable(),

                // TextColumn::make('from_name')
                //     ->searchable(),
                // TextColumn::make('from_email')
                //     ->searchable(),
                // TextColumn::make('to_email')
                //     ->searchable(),

                TextColumn::make("subject")
                    ->label("Email Subject")
                    ->limit(35)
                    ->tooltip(fn($state) => $state)
                    ->searchable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                // ViewAction::make()->label("")->size("lg")->tooltip("View"),
                EditAction::make()->label("")->size("lg")->tooltip("Edit"),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
