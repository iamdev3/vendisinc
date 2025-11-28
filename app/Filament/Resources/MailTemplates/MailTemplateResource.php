<?php

namespace App\Filament\Resources\MailTemplates;

use App\Filament\Resources\MailTemplates\Pages\CreateMailTemplate;
use App\Filament\Resources\MailTemplates\Pages\EditMailTemplate;
use App\Filament\Resources\MailTemplates\Pages\ListMailTemplates;
use App\Filament\Resources\MailTemplates\Pages\ViewMailTemplate;
use App\Filament\Resources\MailTemplates\Schemas\MailTemplateForm;
use App\Filament\Resources\MailTemplates\Schemas\MailTemplateInfolist;
use App\Filament\Resources\MailTemplates\Tables\MailTemplatesTable;
use App\Models\MailTemplate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;

class MailTemplateResource extends Resource
{
    use Translatable;
    protected static ?string $model                             = MailTemplate::class;
    protected static string|BackedEnum|null $navigationIcon     = Heroicon::EnvelopeOpen;
    // protected static ?string $recordTitleAttribute              = 'Mail Template';
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';
    protected static ?int $navigationSort                       = 2;

    public static function form(Schema $schema): Schema
    {
        return MailTemplateForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MailTemplateInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MailTemplatesTable::configure($table);
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
            'index' => ListMailTemplates::route('/'),
            'create' => CreateMailTemplate::route('/create'),
            'view' => ViewMailTemplate::route('/{record}'),
            'edit' => EditMailTemplate::route('/{record}/edit'),
        ];
    }
}
