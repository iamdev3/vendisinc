<?php

namespace App\Models;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class Setting extends Model
{
    protected $table = "settings";
    protected $guarded = ["id"];


    public static function generateConfig(){
        $allSettings = Self::all();
        $groupSettings = [];

        foreach($allSettings as $setting){
            $group =  $setting->group ?? 'default';
            $groupSettings[$group][$setting->key] = $setting->value;
        }

        // Create PHP config file content
        $configContent = "<?php\n\nreturn " . var_export($groupSettings, true) . ";\n";

        // Path to config file
        $configPath = config_path('settings.php');

        // Write to file
        File::put($configPath, $configContent);

        // Update environment variables for mail settings
        self::updateEnvironmentVariables($groupSettings['mail'] ?? []);

        // Clear config cache so Laravel picks up the new file
        Artisan::call('config:clear');

        Notification::make()
            ->title("Settings Config File Updated")
            ->body("settings.php configuration file updated & config cache cleared")
            ->info()
            ->send();

    }

    /**
     * Update environment variables with mail settings
     *
     * @param array $mailSettings
     * @return void
     */
    protected static function updateEnvironmentVariables(array $mailSettings): void
    {
        // Define the essential mail environment variables to update
        $mailEnvVars = [
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_FROM_ADDRESS'
        ];

        // Get the path to the .env file
        $envPath = base_path('.env');

        // Check if .env file exists
        if (!File::exists($envPath)) {
            return;
        }

        // Read the current .env content
        $envContent = File::get($envPath);

        // Update each mail setting
        foreach ($mailEnvVars as $envVar) {
            if (isset($mailSettings[$envVar])) {
                $value = $mailSettings[$envVar];

                // Escape special characters in the value
                $escapedValue = self::escapeEnvValue($value);

                // Check if the variable already exists in .env
                if (preg_match("/^{$envVar}=.*/m", $envContent)) {
                    // Update existing variable
                    $envContent = preg_replace(
                        "/^{$envVar}=.*/m",
                        "{$envVar}={$escapedValue}",
                        $envContent
                    );
                } else {
                    // Add new variable
                    $envContent .= "\n{$envVar}={$escapedValue}";
                }
            }
        }

        // Write the updated content back to .env file
        File::put($envPath, $envContent);
    }

    /**
     * Escape special characters in environment variable values
     *
     * @param string|null $value
     * @return string
     */
    protected static function escapeEnvValue(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        // If value contains spaces or special characters, wrap in quotes
        if (preg_match('/\s|[#\$\\"\'`]/', $value)) {
            // Escape double quotes
            $value = str_replace('"', '\"', $value);
            return "\"{$value}\"";
        }

        return $value;
    }


    /**
     * Get available sections for settings dropdown (config + custom)
     */
    public static function getAvailableSections(): array
    {
        $sections = [];

        // From config
        $configSections = config('app-settings.sections', []);
        foreach ($configSections as $key => $section) {
            $sections[$key] = $section['title'] ?? ucfirst(str_replace('_', ' ', $key));
        }

        // From custom DB groups
        $customGroups = self::
            // where('is_custom', true)
            distinct()
            ->pluck('group')
            ->filter()
            ->mapWithKeys(fn($group) => [$group => ucfirst(str_replace('_', ' ', $group))]);

        return array_merge($sections, $customGroups->toArray());
    }


}
