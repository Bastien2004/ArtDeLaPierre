<?php

use App\Http\Controllers\CalendrierController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\PoidsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DevisController;
use App\Http\Controllers\StocksController;
use App\Http\Controllers\TarifController;
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

    // Gestion des Devis*
    Route::get('/devis/calendrier', [CalendrierController::class, 'index'])
        ->name('devis.calendrier');
    Route::post('/devis/send-email', [DevisController::class, 'sendEmail'])->name('devis.sendEmail');
    Route::resource('devis', DevisController::class);
    Route::get('/devis-pdf/{client}/{date}', [DevisController::class, 'downloadPDF'])
        ->name('devis.downloadPDF');
    Route::post('/devis/update-livraison', [App\Http\Controllers\DevisController::class, 'updateLivraison'])->name('devis.updateLivraison');
    Route::get('/devis/atelier/{client}/{date}', [DevisController::class, 'downloadAtelierPDF'])->name('devis.downloadAtelierPDF');
    Route::get('/emails/search', [EmailController::class, 'search'])->name('emails.search');
    Route::post('/emails', [EmailController::class, 'store'])->name('emails.store');


    // Gestion des Tarifs
    Route::get('/tarifs', [TarifController::class, 'index'])->name('tarifs.tarifs');
    Route::post('/tarifs/update-all', [TarifController::class, 'updateAll'])->name('tarifs.updateAll');
    Route::delete('/tarifs/travail/{id}', [TarifController::class, 'deleteTravail'])->name('tarifs.deleteTravail');
    Route::delete('/tarifs/epaisseur/{ep}', [TarifController::class, 'deleteEpaisseur'])->name('tarifs.deleteEpaisseur');

    // poids
    Route::get('/poids', [PoidsController::class, 'index'])->name('poids.poids');

    // Stocks
    Route::get('/stocks', [StocksController::class, 'index'])->name('stocks.index');
    Route::post('/stocks', [StocksController::class, 'store'])->name('stocks.store');
    Route::put('/stocks/{id}', [StocksController::class, 'update'])->name('stocks.update');
    Route::delete('/stocks/{id}', [StocksController::class, 'destroy'])->name('stocks.destroy');
    Route::get('/stocks/export-pdf', [App\Http\Controllers\StocksController::class, 'exportPdf'])->name('stocks.pdf');
    //Blocs
    Route::post  ('/stocks/blocs',        [StocksController::class, 'storeBloc'])  ->name('stocks.blocs.store');
    Route::put   ('/stocks/blocs/{id}',   [StocksController::class, 'updateBloc']) ->name('stocks.blocs.update');
    Route::delete('/stocks/blocs/{id}',   [StocksController::class, 'destroyBloc'])->name('stocks.blocs.destroy');
    //Cassons
    Route::post  ('/stocks/cassons',        [StocksController::class, 'storeCasson'])  ->name('stocks.cassons.store');
    Route::put   ('/stocks/cassons/{id}',   [StocksController::class, 'updateCasson']) ->name('stocks.cassons.update');
    Route::delete('/stocks/cassons/{id}',   [StocksController::class, 'destroyCasson'])->name('stocks.cassons.destroy');

    // Registre email
    Route::get('/emails', [EmailController::class, 'index'])->name('emails.index');
    Route::delete('/emails/{id}', [EmailController::class, 'destroy'])->name('emails.destroy');
});

require __DIR__.'/auth.php';
