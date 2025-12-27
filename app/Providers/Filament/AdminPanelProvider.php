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
use Caresome\FilamentAuthDesigner\AuthDesignerPlugin;
use Caresome\FilamentAuthDesigner\Enums\AuthLayout;
use Caresome\FilamentAuthDesigner\Enums\MediaDirection;
use Filament\Support\Facades\FilamentView;
use Illuminate\Support\Facades\Blade;

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
                AuthDesignerPlugin::make()
                    ->login(
                        layout: AuthLayout::Split,
                        media: Storage::url(config("settings.general_settings.admin_login_image")) ?? null,
                        direction: MediaDirection::Left,
                        blur: 10
                    )->themeToggle()
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
            ->font('montserrat')
            ->viteTheme('resources/css/filament/admin/theme.css')
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

    // public function boot(): void
    // {
    //     FilamentView::registerRenderHook(
    //         'panels::styles.before',
    //         fn (): string => Blade::render('
    //             <style>
    //                 .fi-sidebar {
    //                     background-color: ' . config('settings.appearance.primary_color', '#f0f0f0') . ' !important;
    //                 }

    //                 .fi-sidebar-group-label {
    //                     color: ' .  '#f0f0f0' . ' !important;
    //                 }

    //                 .fi-sidebar-item-label {
    //                     color: ' .  '#f0f0f0' . ' !important;
    //                 }

    //                 .fi-sidebar-item.fi-active.fi-sidebar-item-has-url {
    //                     text-color: ' .  '#f0f0f0' . ' !important;
    //                     background-color: color-mix(in srgb, ' . config('settings.appearance.primary_color', '#f0f0f0') . ' 15%, white) !important;
    //                     border-radius: 0.5rem;
    //                 }

    //                 .dark .fi-sidebar {
    //                     background-color: ' . config('settings.appearance.primary_color_dark', '#1f2937') . ' !important;
    //                 }
    //             </style>
    //         ')
    //     );
    // }
}
