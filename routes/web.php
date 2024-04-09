<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('zaimeaview::pages.welcome');
});

Route::middleware([
    'auth:sanctum',
    config('zaimea.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('zaimeaview::pages.dashboard');
    })->name('dashboard');
});
