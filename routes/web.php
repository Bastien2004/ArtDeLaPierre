<?php

use App\Http\Controllers\DevisController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('devis', DevisController::class);

Route::delete('/devis/{id}', [DevisController::class, 'destroy'])->name('devis.destroy');

Route::get('/devis-pdf/{client}/{date}', [DevisController::class, 'downloadPDF'])
    ->name('devis.downloadPDF');
