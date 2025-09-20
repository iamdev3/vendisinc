<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocaleController extends Controller
{
    public function switch(Request $request, $locale)
    {
        // Validate the locale
        $availableLocales = ['en', 'gu'];
        
        if (!in_array($locale, $availableLocales)) {
            abort(404);
        }

        // Set the locale in session
        Session::put('locale', $locale);
        
        // Set the application locale
        app()->setLocale($locale);

        // Redirect back to the previous page
        return redirect()->back();
    }
}
