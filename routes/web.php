<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DevisController; // Ne pas oublier l'import
use App\Http\Controllers\TarifController; // Ne pas oublier l'import
use Illuminate\Support\Facades\Route;

// 1. Page d'accueil publique
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. Le Dashboard (Optionnel, Breeze l'utilise par défaut)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. TOUTES LES ROUTES PROTÉGÉES (Connexion requise)
Route::middleware('auth')->group(function () {

    // Profil utilisateur (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- TES OUTILS MÉTIER ---

    // Gestion des Devis
    Route::resource('devis', DevisController::class);
    Route::delete('/devis/{id}', [DevisController::class, 'destroy'])->name('devis.destroy');
    Route::get('/devis-pdf/{client}/{date}', [DevisController::class, 'downloadPDF'])
        ->name('devis.downloadPDF');
    Route::post('/devis/update-livraison', [App\Http\Controllers\DevisController::class, 'updateLivraison'])->name('devis.updateLivraison');

    // Gestion des Tarifs
    Route::get('/tarifs', [TarifController::class, 'index'])->name('tarifs.tarifs');
    Route::post('/tarifs/update-all', [TarifController::class, 'updateAll'])->name('tarifs.updateAll');
    Route::delete('/tarifs/travail/{id}', [TarifController::class, 'deleteTravail'])->name('tarifs.deleteTravail');
    Route::delete('/tarifs/epaisseur/{ep}', [TarifController::class, 'deleteEpaisseur'])->name('tarifs.deleteEpaisseur');
});

require __DIR__.'/auth.php';
