<?php

return [

    "sections" => [

        "general_settings" => [
            "title" => "General Settings",
            "description" => "Basic application settings",
            "icon" => "heroicon-o-cog-6-tooth",
            "columns" => 2,
            "fields" => [
                [
                    "key" => "app_name",
                    "label" => "App Name",
                    "type" => "text",
                    "placeholder" => "Enter App name",
                    "required" => true,
                    // "columnSpanFull"=>false
                ],
                [
                    "key" => "app_email",
                    "label" => "App Email",
                    "type" => "email",
                    "placeholder" => "contact@example.com",
                    "prefix_icon" => "heroicon-o-envelope",
                    "required" => true,
                ],
                [
                    "key" => "app_logo",
                    "label" => "App Logo",
                    "type" => "image",
                    "placeholder" => "Upload Logo image, File: jpg/png | maxsize:2mb",
                    "required" => false,
                ],
                [
                    "key" => "app_favicon",
                    "label" => "App Favicon",
                    "type" => "image",
                    "placeholder" => "Upload Logo image, File: jpg/png | maxsize:2mb",
                    "required" => false,
                ],
                [
                    "key" => "app_description",
                    "label" => "App Description",
                    "type" => "textarea",
                    "columnSpanFull" => true,
                    "placeholder" => "Enter app description",
                    "rows" => 2,
                ],
                // [
                //     "key" => "maintenance_mode",
                //     "label" => "Maintenance Mode",
                //     "type" => "toggle",
                //     "default" => false,
                //     "helper_text" => "Enable to put app in maintenance mode",
                // ],
                [
                    'key'       => 'system_locales',
                    'label'     => 'System Locales',
                    'type'      => 'multi-select',
                    'default'   => "en",
                    "helper_text"   => "Select systems languages to enbale them.",
                    "options"       => [
                        "en" => "English",
                        "gu" => "Gujarati",
                        "es" => "Spanish",
                        "hi" => "Hindi",
                        "de" => "German",
                        "fr" => "french",
                    ],
                    "multiple"   => true,
                    "searchable" => true,
                ]
             
            ],
        ],

        "appearance" => [
            "title" => "App Appearance",
            "description" => "Customize look and feel",
            "icon" => "heroicon-o-paint-brush",
            "fields" => [
                [
                    "key" => "primary_color",
                    "label" => "Primary Color",
                    "type" => "color",
                    "default" => "#3b82f6",
                ],
                [
                    "key" => "default_language",
                    "label" => "Default Language",
                    "type" => "select",
                    "options" => [
                        "en" => "English",
                        "hi" => "Hindi",
                        "gu" => "Gujarati",
                    ],
                    "searchable" => true,
                    "default" => "en",
                ],
                [
                    "key" => "items_per_page",
                    "label" => "Items Per Page",
                    "type" => "number",
                    "min" => 10,
                    "max" => 100,
                    "default" => 25,
                ],
            ],
        ],

        "mail" => [
            "title" => "Mail Settings",
            "description" => "Configure email delivery",
            "icon" => "heroicon-o-envelope",
            "fields" => [
                [
                    "key"   => "MAIL_MAILER",
                    "label" => "Mail Mailer",
                    "type"  => "select",
                    "options" => [
                        "smtp" => "SMTP",
                        "log" => "Log",
                        "array" => "Array",
                    ],
                    "default" => "smtp",
                    "helper_text" => "Choose which mailer to use for sending emails",
                ],
                [
                    "key" => "MAIL_HOST",
                    "label" => "Mail Host",
                    "type" => "text",
                    "placeholder" => "sandbox.smtp.mailtrap.io",
                    "default" => "sandbox.smtp.mailtrap.io",
                    "helper_text" => "SMTP server address",
                ],
                [
                    "key" => "MAIL_PORT",
                    "label" => "Mail Port",
                    "type" => "number",
                    "default" => 2525,
                    "min" => 1,
                    "max" => 65535,
                    "helper_text" => "SMTP server port",
                ],
                [
                    "key" => "MAIL_USERNAME",
                    "label" => "Mail Username",
                    "type" => "text",
                    "placeholder" => "your-username",
                    "default" => null,
                    "helper_text" => "SMTP username for authentication",
                ],
                [
                    "key" => "MAIL_PASSWORD",
                    "label" => "Mail Password",
                    "type" => "password",
                    "default" => null,
                    "helper_text" => "SMTP password for authentication",
                ],
                [
                    "key" => "MAIL_FROM_ADDRESS",
                    "label" => "Mail From Address",
                    "type" => "email",
                    "placeholder" => "hello@example.com",
                    "default" => "hello@example.com",
                    "helper_text" => "Email address that appears in the 'from' field",
                ],
            ],
        ],

    ],

];