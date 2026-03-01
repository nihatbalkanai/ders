<?php

use Illuminate\Support\Facades\Route;

// SPA catch-all - Vue Router handles all routing
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
