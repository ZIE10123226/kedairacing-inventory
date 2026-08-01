<?php

use Illuminate\Support\Facades\Route;

// Semua route web akan diarahkan ke SPA entry (app.blade.php)
// Route API akan dipisah di routes/api.php
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
