<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('filament.admin.auth.login');
});

// AJAX route for fetching retailer details
Route::get('/api/retailor-details', [Controller::class, 'getRetailorDetails'])->name('api.retailor-details');


