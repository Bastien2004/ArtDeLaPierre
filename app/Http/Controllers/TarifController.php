<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\TravailTarif; // Importation du modèle indispensable
use Illuminate\Http\Request;

class TarifController extends Controller
{
    /**
     * Affiche la page de gestion des tarifs
     */
    public function index()
    {
        // On récupère les tarifs de pierre
        $allTarifs = Tarif::all();

        // AJOUT : On récupère aussi les tarifs de travaux (Rejingot, etc.)
        $tarifsTravaux = TravailTarif::all();

        // On envoie les deux variables à la vue
        return view('tarifs.tarifs', compact('allTarifs', 'tarifsTravaux'));
    }

    /**
     * Met à jour tous les prix d'un coup (Pierres + Travaux)
     */
    public function updateAll(Request $request)
    {
        // 1. Mise à jour des tarifs Pierre Bleue
        if ($request->has('prix')) {
            foreach ($request->prix as $id => $valeur) {
                if ($valeur !== null) {
                    Tarif::where('id', $id)->update(['prix_m2' => $valeur]);
                }
            }
        }

        // 2. Mise à jour des travaux spécifiques
        if ($request->has('travaux')) {
            foreach ($request->travaux as $id => $valeur) {
                if ($valeur !== null) {
                    TravailTarif::where('id', $id)->update(['prix' => $valeur]);
                }
            }
        }

        return redirect()->back()->with('success', 'Toute la grille tarifaire a été mise à jour avec succès !');
    }
}
