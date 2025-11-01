<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route("filament.admin.auth.login");
});

// Test translation route
Route::get('/test-translation', function () {
    return view('test-translation');
});
