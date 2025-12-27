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
use Filament\Forms\Components\Checkbox;
use App\Models\Setting;


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
                ->columns([
                    'sm' => 1,  // 1 column on mobile
                    'md' => 2,  // 2 columns on medium screens and larger
                ])
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
            ->extraAttributes(['class' => 'settings-tabs-responsive'])
            ->vertical(fn() => !str_contains(strtolower(request()->userAgent() ?? ''), 'mobile'))
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


    /**
     * Schema for adding custom fields with dynamic attributes
     */
    public static function getAddFieldSchema(): array
    {
        return [
            Section::make('Section Configuration')
                ->schema([
                    Radio::make('field_addition_type')
                        ->label('Add Field To')
                        ->options([
                            'existing_section' => 'Existing Section',
                            'new_section' => 'New Section',
                        ])
                        ->default('existing_section')
                        ->required()
                        ->live()
                        ->columnSpanFull(),

                    Select::make('group')
                        ->label('Select Section')
                        ->options(Setting::getAvailableSections())
                        ->required()
                        ->searchable()
                        ->visible(fn($get) => $get('field_addition_type') === 'existing_section'),

                    TextInput::make('new_group')
                        ->label('Section Key')
                        ->helperText('Lowercase, underscores only (e.g., custom_section)')
                        ->required()
                        ->regex('/^[a-z_]+$/')
                        ->visible(fn($get) => $get('field_addition_type') === 'new_section'),

                    TextInput::make('new_group_title')
                        ->label('Section Title')
                        ->required()
                        ->visible(fn($get) => $get('field_addition_type') === 'new_section'),

                    Textarea::make('new_group_description')
                        ->label('Section Description')
                        ->rows(2)
                        ->visible(fn($get) => $get('field_addition_type') === 'new_section'),

                    TextInput::make('new_group_icon')
                        ->label('Section Icon')
                        ->placeholder('heroicon-o-cog-6-tooth')
                        ->default('heroicon-o-cog-6-tooth')
                        ->visible(fn($get) => $get('field_addition_type') === 'new_section'),
                ])
                ->columns(2),

            Section::make('Basic Field Configuration')
                ->schema([
                    TextInput::make('key')
                        ->label('Field Key')
                        ->helperText('Lowercase, underscores (e.g., custom_footer_text)')
                        ->required()
                        ->unique('settings', 'key')
                        ->regex('/^[a-z_]+$/')
                        ->live(debounce: 500),

                    TextInput::make('label')
                        ->label('Field Label')
                        ->required(),

                    Select::make('type')
                        ->label('Field Type')
                        ->options([
                            'text' => 'Text Input',
                            'email' => 'Email',
                            'password' => 'Password',
                            'number' => 'Number',
                            'textarea' => 'Textarea',
                            'richtext' => 'Rich Text Editor',
                            'color' => 'Color Picker',
                            'select' => 'Select Dropdown',
                            'multi-select' => 'Multi Select',
                            'toggle' => 'Toggle Switch',
                            'radio' => 'Radio Buttons',
                            'image' => 'Image Upload',
                        ])
                        ->required()
                        ->live()
                        ->searchable()
                        ->columnSpanFull(),

                    Toggle::make('required')
                        ->label('Required Field')
                        ->default(false),

                    Toggle::make('columnSpanFull')
                        ->label('Full Width')
                        ->helperText('Field takes full width of the form')
                        ->default(false),
                ])
                ->columns(2),

            // Text Input Specific Attributes
            Section::make('Text Input Settings')
                ->schema([
                    TextInput::make('placeholder')
                        ->label('Placeholder Text')
                        ->placeholder('Enter placeholder...'),

                    TextInput::make('prefix_icon')
                        ->label('Prefix Icon')
                        ->placeholder('heroicon-o-envelope')
                        ->helperText('Heroicon name to show before input'),

                    TextInput::make('suffix_icon')
                        ->label('Suffix Icon')
                        ->placeholder('heroicon-o-information-circle')
                        ->helperText('Heroicon name to show after input'),

                    TextInput::make('default')
                        ->label('Default Value'),
                ])
                ->columns(2)
                ->visible(fn($get) => in_array($get('type'), ['text', 'email', 'password']))
                ->collapsible(),

            // Number Input Specific Attributes
            Section::make('Number Input Settings')
                ->schema([
                    TextInput::make('placeholder')
                        ->label('Placeholder Text'),

                    TextInput::make('default')
                        ->label('Default Value')
                        ->numeric(),

                    TextInput::make('min')
                        ->label('Minimum Value')
                        ->numeric(),

                    TextInput::make('max')
                        ->label('Maximum Value')
                        ->numeric(),

                    TextInput::make('step')
                        ->label('Step')
                        ->numeric()
                        ->default(1)
                        ->helperText('Increment/decrement value'),

                    TextInput::make('prefix_icon')
                        ->label('Prefix Icon')
                        ->placeholder('heroicon-o-currency-dollar'),
                ])
                ->columns(3)
                ->visible(fn($get) => $get('type') === 'number')
                ->collapsible(),

            // Textarea Specific Attributes
            Section::make('Textarea Settings')
                ->schema([
                    TextInput::make('placeholder')
                        ->label('Placeholder Text'),

                    TextInput::make('rows')
                        ->label('Number of Rows')
                        ->numeric()
                        ->default(3)
                        ->minValue(2)
                        ->maxValue(20),

                    TextInput::make('default')
                        ->label('Default Value'),
                ])
                ->columns(3)
                ->visible(fn($get) => $get('type') === 'textarea')
                ->collapsible(),

            // Select & Multi-Select Specific Attributes
            Section::make('Select Field Settings')
                ->description('Configure dropdown options and behavior')
                ->schema([
                    Toggle::make('searchable')
                        ->label('Searchable')
                        ->helperText('Allow users to search in options')
                        ->default(false),

                    Toggle::make('multiple')
                        ->label('Allow Multiple Selection')
                        ->default(fn($get) => $get('type') === 'multi-select')
                        ->disabled(fn($get) => $get('type') === 'multi-select')
                        ->visible(fn($get) => $get('type') === 'select'),

                    TextInput::make('placeholder')
                        ->label('Placeholder Text')
                        ->default('Select an option')
                        ->columnSpanFull(),

                    Textarea::make('options')
                        ->label('Options')
                        ->helperText('Enter one option per line in format: key|Label (e.g., "en|English" or just "English")')
                        ->placeholder("key1|Label 1\nkey2|Label 2\nkey3|Label 3")
                        ->required()
                        ->rows(6)
                        ->columnSpanFull(),

                    TextInput::make('default')
                        ->label('Default Selected Value')
                        ->helperText('Enter the key of default option')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->visible(fn($get) => in_array($get('type'), ['select', 'multi-select']))
                ->collapsible(),

            // Radio Specific Attributes
            Section::make('Radio Button Settings')
                ->schema([
                    Textarea::make('options')
                        ->label('Options')
                        ->helperText('Enter one option per line in format: key|Label (e.g., "yes|Yes" or just "Yes")')
                        ->placeholder("option1|Option 1\noption2|Option 2\noption3|Option 3")
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),

                    TextInput::make('default')
                        ->label('Default Selected Value')
                        ->helperText('Enter the key of default option'),

                    Toggle::make('inline')
                        ->label('Display Inline')
                        ->helperText('Show options horizontally instead of vertically')
                        ->default(false),
                ])
                ->columns(2)
                ->visible(fn($get) => $get('type') === 'radio')
                ->collapsible(),

            // Toggle Specific Attributes
            Section::make('Toggle Settings')
                ->schema([
                    Toggle::make('default')
                        ->label('Default State')
                        ->helperText('Toggle ON by default')
                        ->default(false),

                    TextInput::make('on_icon')
                        ->label('ON Icon')
                        ->placeholder('heroicon-o-check-circle'),

                    TextInput::make('off_icon')
                        ->label('OFF Icon')
                        ->placeholder('heroicon-o-x-circle'),

                    TextInput::make('on_color')
                        ->label('ON Color')
                        ->placeholder('success')
                        ->helperText('success, danger, warning, info, primary'),

                    TextInput::make('off_color')
                        ->label('OFF Color')
                        ->placeholder('gray'),
                ])
                ->columns(2)
                ->visible(fn($get) => $get('type') === 'toggle')
                ->collapsible(),

            // Color Picker Specific Attributes
            Section::make('Color Picker Settings')
                ->schema([
                    TextInput::make('default')
                        ->label('Default Color')
                        ->placeholder('#3b82f6')
                        ->helperText('Hex color code'),

                    Toggle::make('format')
                        ->label('RGB Format')
                        ->helperText('Use RGB format instead of HEX')
                        ->default(false),
                ])
                ->columns(2)
                ->visible(fn($get) => $get('type') === 'color')
                ->collapsible(),

            // Image Upload Specific Attributes
            Section::make('Image Upload Settings')
                ->schema([
                    TextInput::make('disk')
                        ->label('Storage Disk')
                        ->default('public')
                        ->helperText('public, s3, etc.'),

                    TextInput::make('directory')
                        ->label('Upload Directory')
                        ->default('images')
                        ->helperText('Folder path in storage'),

                    TextInput::make('maxSize')
                        ->label('Max File Size (KB)')
                        ->numeric()
                        ->default(2048)
                        ->helperText('Maximum upload size in kilobytes'),

                    Toggle::make('multiple')
                        ->label('Allow Multiple Images')
                        ->default(false),

                    TextInput::make('imagePreviewHeight')
                        ->label('Preview Height (px)')
                        ->numeric()
                        ->default(250),
                ])
                ->columns(2)
                ->visible(fn($get) => $get('type') === 'image')
                ->collapsible(),

            // Common Helper Text (for all fields)
            Section::make('Additional Configuration')
                ->schema([
                    Textarea::make('helper_text')
                        ->label('Helper Text')
                        ->helperText('Descriptive text shown below the field')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->collapsible()
                ->collapsed(),
        ];
    }
}
