<?php

namespace App\Filament\Resources\MailTemplates\Schemas;

use App\Filament\Forms\Components\UnlayerEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use ZPMLabs\FilamentUnlayer\Forms\Components\Unlayer;

class MailTemplateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make("Basic Template Details")
                    ->description("Configure the email template details")
                    ->iconColor("primary")
                    ->icon('heroicon-o-envelope')
                    ->schema([

                        TextInput::make('name')
                            ->label("Email Name")
                            ->placeholder("Enter template purpose, ex: Welcome Email")
                            ->required(),

                        TextInput::make('code')
                            ->label("Template Identifier")
                            ->helperText("Enter The Template Identifier slug, use only letters, numbers, underscores, and hyphens without spaces. Ex: welcome_email ")
                            ->visibleOn("create")
                            ->regex('/^[a-z0-9_\-]+$/')
                            ->required(),

                        TextInput::make('blade_file')
                            ->placeholder("Enter the blade file name, without space ex: wellcome_mail")
                            ->prefixIcon("heroicon-o-document")
                            ->visibleOn("create")
                            ->label("Email Blade File Reference"),

                        TextInput::make('subject')
                            ->placeholder("Enter the email subject")
                            ->label("Email Mail Subject"),

                        TextInput::make('from_name')
                            ->prefixIcon("heroicon-o-user")
                            ->label("From Name")
                            ->placeholder("Enter the sender's name"),

                        TextInput::make('from_email')
                            ->label("From Email")
                            ->prefixIcon("heroicon-o-envelope")
                            ->placeholder("Enter the sender's email id")
                            ->email(),

                        TagsInput::make("cc")
                            ->label("CC Emails")
                            ->prefixIcon("heroicon-o-envelope")
                            ->placeholder("Enter cc email addresses, Write Address and enter to add"),

                        TagsInput::make("bcc")
                            ->label("BCC Emails")
                            ->prefixIcon("heroicon-o-envelope")
                            ->placeholder("Enter bcc email addresses, Write Address and enter to add"),

                        Textarea::make('description')
                            ->label("Email Description For Reference")
                            ->columnSpanFull()
                            ->disabledOn("edit")
                            ->rows(2)
                            ->placeholder("Enter the basic description of what email is about for panel user's reference")
                            ->maxLength(120),

                        Toggle::make("is_active")
                            ->label("Is Template in use")
                            ->default(true)
                            ->hidden(),

                    ])->columnSpanFull()
                    ->columns(2),

                Section::make("Template Content")
                    ->description("Manage Template Content")
                    ->iconColor("primary")
                    ->icon('heroicon-o-envelope')
                    ->schema([

                        // RichEditor::make('content')
                        //     ->label("Email Content"),

                        Unlayer::make('content')->required()

                        // UnlayerEditor::make('content')
                        //     ->label("Email Content")
                        //     ->minHeight('600px')
                        //     ->projectId(config('services.unlayer.project_id'))
                        //     ->columnSpanFull()
                        //     ->helperText('Design your email using the visual editor'),

                    ])->columnSpanFull(),


            ]);
    }
}
