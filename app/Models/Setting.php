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
        
        // Clear config cache so Laravel picks up the new file
        Artisan::call('config:clear');

        Notification::make()
            ->title("Settings Config File Updated")
            ->body("settings.php configuration file updated & config cache cleared")
            ->info()
            ->send();

    }
}
