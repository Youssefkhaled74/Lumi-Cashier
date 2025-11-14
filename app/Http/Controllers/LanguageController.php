<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch application language
     */
    public function switch(string $locale)
    {
        // Validate and set locale
        if (in_array($locale, ['en', 'ar'])) {
            Session::put('locale', $locale);
            App::setLocale($locale);
        }
        
        // Get the previous URL, but prevent redirect loops
        $previousUrl = url()->previous();
        $currentUrl = url()->current();
        
        // If previous URL is the same as current (language switcher), redirect to dashboard
        if ($previousUrl === $currentUrl || str_contains($previousUrl, '/lang/')) {
            return redirect()->route('admin.dashboard');
        }
        
        return redirect($previousUrl);
    }
}
