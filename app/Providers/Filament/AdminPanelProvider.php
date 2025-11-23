<?php

namespace App\Providers\Filament;

use Filament\Pages\Dashboard;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\View\PanelsRenderHook;
use Filament\Forms\Components\Select;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use App\Filament\Pages\Login;
use Illuminate\Support\Facades\Storage;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class,)
            ->passwordReset()
            ->brandLogo(function(){
                $logo = Storage::url(config("settings.general_settings.app_logo")) ?? null;
                return $logo;
            })
            ->brandLogoHeight("4.5rem")
            ->favicon(fn()=> Storage::url(config("settings.general_settings.app_favicon")) ?? null)
            ->brandName('Vendisync')
            ->colors([
                'primary' => config('settings.appearance.primary_color') ?? Color::Indigo,
                'gray'    => Color::Slate
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // AccountWidget::class,
                // FilamentInfoWidget::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                SpatieTranslatablePlugin::make()
                    ->defaultLocales(['en', 'gu']),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationGroups([
                "Orders",
                "Brand Management",
                "Customers Management",
                "Settings",
            ])
            ->sidebarCollapsibleOnDesktop()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ]);
    }
}
