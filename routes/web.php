<?php

use App\Http\Controllers\DevisController;
use App\Http\Controllers\TarifController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('devis', DevisController::class);

Route::delete('/devis/{id}', [DevisController::class, 'destroy'])->name('devis.destroy');

Route::get('/devis-pdf/{client}/{date}', [DevisController::class, 'downloadPDF'])
    ->name('devis.downloadPDF');

// Page d'affichage des tarifs
Route::get('/tarifs', [TarifController::class, 'index'])->name('tarifs.tarifs');

// Action de sauvegarde (POST)
Route::post('/tarifs/update-all', [TarifController::class, 'updateAll'])->name('tarifs.updateAll');
