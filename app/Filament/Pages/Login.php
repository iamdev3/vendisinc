<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use AbanoubNassem\FilamentGRecaptchaField\Forms\Components\GRecaptcha;

class Login extends \Filament\Auth\Pages\Login
{
    // protected static ?string $navigationIcon = 'heroicon-o-document-text';
    // protected static string $view = 'filament.pages.login';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                 $this->makeForm()
                    ->components([

                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                        GRecaptcha::make('captcha')
                        ->visible(env('RECAPTCHA_ENABLED', true))


                    ])->statePath('data'),
            ),
        ];
    }
}