<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Global configuration for all panels
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['en', 'gu'])
                ->labels([
                    'en' => 'English',
                    'gu' => 'ગુજરાતી',
                ])
                ->circular()
                ->visible(insidePanels: true, outsidePanels: false);
        });
    }
}
