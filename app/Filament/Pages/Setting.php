<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Setting extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Setting';
    protected static ?string $navigationGroup = 'Setting';
    protected static ?int $navigationSort = 1;

    protected ?string $heading = "App Setting";
    protected ?string $subheading = "Manage all basic app setting";

    protected static string $view = 'filament.pages.setting';




    public static function canAccess():bool{
        // return auth()->user()->hasRole(['admin','super-admin']);
        return true;
    }
}
