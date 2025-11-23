<?php

namespace App\Filament\Pages;

use App\Helpers\FormFieldBuilder;
use App\Models\Setting as ModelsSetting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use UnitEnum;

class Setting extends Page
{
    protected string $view = 'filament.pages.setting';
    protected static string | BackedEnum | null $navigationIcon = "heroicon-o-cog";
    protected static string | UnitEnum | null $navigationGroup = "Settings";
    protected ?string $subheading = 'Manage all app related settings here';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = ModelsSetting::pluck('value', 'key')->toArray();
        $this->form->fill($settings);
    }

    public function form(Schema $schema): Schema
    {   
        #Get all sections from config
        $configSections = config('app-settings.sections');

        return $schema
                ->components([
                    Form::make([
                        // Use our helper to build tabs from config
                        FormFieldBuilder::buildTabsFromConfig($configSections),
                    ])
                    ->footer([
                        Actions::make([
                            Action::make('save')
                                ->label('Save Settings')
                                ->action(fn()=> $this->save()),
                        ]),
                    ]),
                ])
                ->statePath('data');
    }

    /**
     * Build a map of field keys to their group names
     * This allows us to know which group each field belongs to when saving
     * 
     * @return array - ['site_name' => 'general_settings', 'smtp_host' => 'mail', ...]
     */
    private function getFieldGroupMap(): array
    {
        static $map = null;
        
        // Build map only once and cache it
        if ($map === null) {
            $map = [];
            $configSections = config('app-settings.sections');
            
            // Loop through each section and its fields
            foreach ($configSections as $groupName => $sectionData) {
                foreach ($sectionData['fields'] as $fieldConfig) {
                    // Map field key to group name
                    $map[$fieldConfig['key']] = $groupName;
                }
            }
        }
        
        return $map;
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Get field-to-group mapping
        $fieldGroupMap = $this->getFieldGroupMap();

        // dd($data);
        // Save each field with its group
        foreach ($data as $key => $value) {
            ModelsSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value ?? null,
                    'group' => $fieldGroupMap[$key] ?? 'default',  // ✅ Get group from map
                ]
            );
        }

        #generate inapp config (settings.php)
        ModelsSetting::generateConfig();

        Notification::make()
            ->success()
            ->title("Settings Saved Successfully")
            // ->body("Your settings have been saved successfully.")
            ->send();
    }
}
