<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('landing'); // Your main blade file
})->where('any', '.*');
