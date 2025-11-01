<?php

namespace App\Filament\Pages;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;

class Setting extends Page implements HasForms
{

    use InteractsWithForms;

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

    //public livewire property to access form
    public ?array $setting = [];

    public function mount():void{

        $configSetting = config("app-settings");

        // foreach ($config as $key => $section) {
        //     foreach($section['tab'] as $tab) {



        //     }

        // }

        $this->form->fill();
    }

    public function form(Form $form): Form{

        return $form
        ->schema([

            Section::make('Heading')
                ->description('')
                ->schema([


            Tabs::make("Tabs")
                ->tabs([

                    Tabs\Tab::make('general_settings')
                    ->schema([

                        TextInput::make('app_name')
                            ->label(__('App Name'))
                            ->placeholder('Enter App Name')
                            ->nullable(),

                        TextInput::make('app_email')
                            ->label(__('Email'))
                            ->required(),

                        TextInput::make('app_phone')
                            ->label(__('Phone'))
                            ->required(),

                    ]),

                    Tabs\Tab::make('app_social')
                        ->schema([

                            TextInput::make('app_facebook')
                                ->label(__('Facebook'))
                                ->nullable(),

                            TextInput::make('app_twitter')
                                ->label(__('Twitter'))
                                ->nullable(),

                            TextInput::make('app_instagram')
                                ->label(__('Instagram'))
                        ])

                        ])

                ])->footerActions([
                        Action::make('save')
                            ->label('Save Settings')
                            ->button()
                            ->action(fn () => $this->save()), // ✅ Fix here
                ]),


        ])
        ->statePath('setting');

    }

    public function save(){
        dd($this->setting);

        foreach($this->setting as $key => $value){
            dump($key, $value);
        }
    }

}
