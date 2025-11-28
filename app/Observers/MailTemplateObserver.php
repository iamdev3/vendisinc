<?php

namespace App\Observers;

use App\Models\MailTemplate;
use Illuminate\Support\Facades\File;

class MailTemplateObserver
{   
    public $systemLocales;

    public function __construct()
    {
        $locales             = config('settings.general_settings.system_locales', ["en"]);
        $this->systemLocales = json_decode($locales, true);
    }

    /**
     * Handle the MailTemplate "created" event.
     */
    public function created(MailTemplate $mailTemplate): void
    {
        //
    }

    /**
     * Handle the MailTemplate "updated" event.
     */
    public function updated(MailTemplate $mailTemplate): void
    {
        // dd($mailTemplate);
    }

    public function saved(MailTemplate $mailTemplate): void
    {
        // Get all translations for content field
        $translatableContent = $mailTemplate->getTranslations('content');
        $sysLocales          = $this->systemLocales ;

        //valid locales
        $validLocales = array_filter($sysLocales, function ($locale) use ($translatableContent) {
            return array_key_exists($locale, $translatableContent);
        });

        foreach ($validLocales as $locale) {

            //get content locale wise
            $localeContent = $translatableContent[$locale];
            $html          = $localeContent['html'] ?? null;

            if (!empty($html)) {

                //Directory Path
                $directory  = resource_path("views/emails/{$locale}");

                // Create directory if doesn't exist
                if(!File::isDirectory($directory)){
                    File::makeDirectory($directory, 0777, true);
                }

                //File Path
                $filepath = "{$directory}/{$mailTemplate->blade_file}.blade.php";

                // Write HTML to file
                File::put($filepath, $html);

                \Log::info("Blade file created: {$filepath}");
 
            }

        }

       
        
    }
    /**
     * Handle the MailTemplate "deleted" event.
     */
    public function deleted(MailTemplate $mailTemplate): void
    {
        $sysLocales = $this->systemLocales;

        foreach ($sysLocales as $locale) {
            $filePath = resource_path("views/emails/{$locale}/{$mailTemplate->blade_file}.blade.php");
            
            if (File::exists($filePath)) {
                File::delete($filePath);
                \Log::info("Blade file deleted: {$filePath}");
            }
        }
    }

    /**
     * Handle the MailTemplate "restored" event.
     */
    public function restored(MailTemplate $mailTemplate): void
    {
        //
    }

    /**
     * Handle the MailTemplate "force deleted" event.
     */
    public function forceDeleted(MailTemplate $mailTemplate): void
    {
        //
    }
}