<?php

namespace App\Filament\Resources\MailTemplates\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MailTemplateInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('identifier'),
                TextEntry::make('from_name')
                    ->placeholder('-'),
                TextEntry::make('from_email')
                    ->placeholder('-'),
                TextEntry::make('to_email')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
