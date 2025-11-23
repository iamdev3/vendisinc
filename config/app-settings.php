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
                [
                    "key" => "maintenance_mode",
                    "label" => "Maintenance Mode",
                    "type" => "toggle",
                    "default" => false,
                    "helper_text" => "Enable to put app in maintenance mode",
                ],
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
                    "key" => "smtp_host",
                    "label" => "SMTP Host",
                    "type" => "text",
                    "placeholder" => "smtp.gmail.com",
                ],
                [
                    "key" => "smtp_port",
                    "label" => "SMTP Port",
                    "type" => "number",
                    "default" => 587,
                    "min" => 1,
                    "max" => 65535,
                ],
                [
                    "key" => "smtp_password",
                    "label" => "SMTP Password",
                    "type" => "password",
                ],
            ],
        ],

    ],

];
