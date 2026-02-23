<?php

use App\Http\Controllers\DevisController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('devis', DevisController::class);
