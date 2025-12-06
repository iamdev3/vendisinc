<?php

namespace App\Helpers;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

class FormFieldBuilder
{
    /**
     * Build tabs from configuration sections
     *
     * This function takes the settings config and creates:
     * - A Tab for each section (General Settings, Mail, etc.)
     * - A Section inside each tab with heading and description
     * - Fields inside each section based on field configuration
     *
     * Expected data structure:
     * [
     *   "general_settings" => [
     *       "title" => "General Settings",
     *       "description" => "Basic application settings",
     *       "icon" => "heroicon-o-cog-6-tooth",
     *       "fields" => [
     *           [
     *               "key" => "site_name",
     *               "label" => "Site Name",
     *               "type" => "text",
     *               "placeholder" => "Enter site name",
     *               "required" => true,
     *           ],
     *       ],
     *   ],
     * ]
     *
     * @param array $configSections - Array of section configurations from config file
     * @return Tabs - Complete Tabs component with all sections and fields
     */
    public static function buildTabsFromConfig(array $configSections): Tabs
    {
        // Array to hold all tab components
        $allTabs = [];

        // Loop through each section (e.g., general_settings, mail, appearance)
        foreach($configSections as $sectionKey => $sectionData) {

            // STEP 1: Build all field components for this section
            $sectionFields = [];

            // Loop through each field definition in this section
            foreach($sectionData['fields'] as $fieldConfig) {
                // Render the field component and add to array
                $sectionFields[] = self::renderField($fieldConfig);
            }

            // STEP 2: Create a Section component to wrap the fields
            $sectionComponent = Section::make()
                ->heading($sectionData['title'] ?? ucfirst(str_replace('_', ' ', $sectionKey)))
                ->description($sectionData['description'] ?? '')
                ->icon($sectionData['icon'] ?? 'heroicon-o-cog-6-tooth')
                ->iconColor('primary')
                ->columns($sectionData['columns'] ?? 2)
                ->columnSpanFull()
                ->schema($sectionFields);  // ✅ Pass fields array directly (not wrapped)

            // STEP 3: Create a Tab component and put the Section inside
            $tabComponent = Tab::make($sectionData['title'] ?? ucfirst(str_replace('_', ' ', $sectionKey)))
                ->icon($sectionData['icon'] ?? 'heroicon-o-cog-6-tooth')
                ->schema([$sectionComponent]);

            // Add this tab to the collection
            $allTabs[] = $tabComponent;
        }

        // STEP 4: Return complete Tabs component with all tabs
        return Tabs::make("Settings Tabs")
            ->tabs($allTabs)
            ->vertical()
            ->contained();
    }

    /**
     * Render a single form field based on its configuration
     *
     * This function takes a field config array and returns the appropriate
     * Filament form component with all attributes applied.
     *
     * @param array $config - Field configuration (key, type, label, etc.)
     * @return mixed - Filament form component (TextInput, Select, etc.)
     */
    public static function renderField(array $fieldDefinition)
    {
        // Match the field type and create the appropriate component
        $fieldComponent = match($fieldDefinition['type']) {

            'text' => TextInput::make($fieldDefinition['key'])
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->placeholder($fieldDefinition['placeholder'] ?? null)
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? null)
                ->helperText($fieldDefinition['helper_text'] ?? null)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->prefixIcon($fieldDefinition['prefix_icon'] ?? null),

            'email' => TextInput::make($fieldDefinition['key'])
                ->email()
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->placeholder($fieldDefinition['placeholder'] ?? null)
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? null)
                ->helperText($fieldDefinition['helper_text'] ?? null)
                ->prefixIcon($fieldDefinition['prefix_icon'] ?? null)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                ),

            'password' => TextInput::make($fieldDefinition['key'])
                ->password()
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->placeholder($fieldDefinition['placeholder'] ?? null)
                ->required($fieldDefinition['required'] ?? false)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->helperText($fieldDefinition['helper_text'] ?? null),

            'number' => TextInput::make($fieldDefinition['key'])
                ->numeric()
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->placeholder($fieldDefinition['placeholder'] ?? null)
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? null)
                ->minValue($fieldDefinition['min'] ?? null)
                ->maxValue($fieldDefinition['max'] ?? null)
                ->step($fieldDefinition['step'] ?? null)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->helperText($fieldDefinition['helper_text'] ?? null),

            'textarea' => Textarea::make($fieldDefinition['key'])
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->placeholder($fieldDefinition['placeholder'] ?? null)
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? null)
                ->rows($fieldDefinition['rows'] ?? 3)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->helperText($fieldDefinition['helper_text'] ?? null),

            'richtext' => RichEditor::make($fieldDefinition['key'])
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? null)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->helperText($fieldDefinition['helper_text'] ?? null),

            'color' => ColorPicker::make($fieldDefinition['key'])
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? null)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->helperText($fieldDefinition['helper_text'] ?? null),

            'select' => Select::make($fieldDefinition['key'])
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->options($fieldDefinition['options'] ?? [])
                ->searchable($fieldDefinition['searchable'] ?? false)
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? null)
                ->helperText($fieldDefinition['helper_text'] ?? null)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->placeholder($fieldDefinition['placeholder'] ?? 'Select an option'),

            'multi-select' => Select::make($fieldDefinition['key'])
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->options($fieldDefinition['options'] ?? [])
                ->searchable($fieldDefinition['searchable'] ?? false)
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? null)
                ->helperText($fieldDefinition['helper_text'] ?? null)
                ->multiple($fieldDefinition['multiple'] ?? false)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->dehydrateStateUsing(fn($state) =>  json_encode($state))
                ->placeholder($fieldDefinition['placeholder'] ?? 'Select an option'),

            'toggle' => Toggle::make($fieldDefinition['key'])
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? false)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->helperText($fieldDefinition['helper_text'] ?? null),

            'radio' => Radio::make($fieldDefinition['key'])
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->options($fieldDefinition['options'] ?? [])
                ->required($fieldDefinition['required'] ?? false)
                ->default($fieldDefinition['default'] ?? null)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->helperText($fieldDefinition['helper_text'] ?? null),

            'image' => FileUpload::make($fieldDefinition['key'])
                        ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                        ->placeholder($fieldDefinition['placeholder'] ?? null)
                        ->required($fieldDefinition['required'] ?? false)
                        ->disk($fieldDefinition['disk'] ?? 'public')
                        ->directory($fieldDefinition['directory'] ?? 'images')
                        ->image()
                        ->maxSize($fieldDefinition['maxSize'] ?? 1080*2)
                        ->default($fieldDefinition['default'] ?? null)
                        ->imagePreviewHeight('250')
                        ->when(
                            $fieldDefinition['columnSpanFull'] ?? false,
                            fn($field) => $field->columnSpanFull()
                        )
                        ->helperText($fieldDefinition['helperText'] ?? null),

            default => TextInput::make($fieldDefinition['key'])
                ->label($fieldDefinition['label'] ?? ucfirst($fieldDefinition['key']))
                ->placeholder($fieldDefinition['placeholder'] ?? null)
                ->when(
                    $fieldDefinition['columnSpanFull'] ?? false,
                    fn($field) => $field->columnSpanFull()
                )
                ->required($fieldDefinition['required'] ?? false),
        };

        return $fieldComponent;
    }
}
