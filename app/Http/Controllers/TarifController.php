<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\TravailTarif;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    /**
     * Affiche la page de gestion des tarifs
     */
    public function index()
    {
        $allTarifs = Tarif::all();
        $tarifsTravaux = TravailTarif::all();

        return view('tarifs.tarifs', compact('allTarifs', 'tarifsTravaux'));
    }

    /**
     * Met à jour et Ajoute les tarifs (Pierres + Travaux)
     */
    public function updateAll(Request $request)
    {
        // 1. MISE À JOUR DES EXISTANTS (Prix M2 des pierres)
        if ($request->has('prix')) {
            foreach ($request->prix as $id => $valeur) {
                if ($valeur !== null) {
                    Tarif::where('id', $id)->update(['prix_m2' => $valeur]);
                }
            }
        }

        // 2. MISE À JOUR DES EXISTANTS (Travaux spécifiques)
        if ($request->has('travaux')) {
            foreach ($request->travaux as $id => $valeur) {
                if ($valeur !== null) {
                    TravailTarif::where('id', $id)->update(['prix' => $valeur]);
                }
            }
        }

        // 3. AJOUT D'UNE NOUVELLE ÉPAISSEUR
        // On vérifie si l'utilisateur a rempli le champ "new_epaisseur"
        if ($request->filled('new_epaisseur')) {
            $ep = $request->new_epaisseur;
            $finitions = ['Adoucie P40', 'Brut de sciage', 'Adoucie Foncé', 'Ciselé'];
            $types = ['Particulier', 'Entreprise'];

            foreach ($types as $type) {
                foreach ($finitions as $finition) {
                    // firstOrCreate évite les doublons si l'épaisseur existe déjà
                    Tarif::firstOrCreate([
                        'type_client' => $type,
                        'finition'    => $finition,
                        'epaisseur'   => $ep
                    ], [
                        'prix_m2'     => 0 // Prix par défaut à 0€
                    ]);
                }
            }
        }

        // 4. AJOUT D'UN NOUVEAU TRAVAIL / OPTION
        if ($request->filled('new_travail_nom')) {
            TravailTarif::create([
                'nom'   => $request->new_travail_nom,
                'unite' => $request->new_travail_unite,
                'prix'  => $request->new_travail_prix ?? 0
            ]);
        }

        return redirect()->back()->with('success', 'La grille tarifaire a été mise à jour et les nouveaux éléments ont été ajoutés !');
    }

    /**
     * Supprimer un travail spécifique (Optionnel mais recommandé)
     */
    public function destroyTravail($id)
    {
        TravailTarif::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'L\'option a été supprimée.');
    }

    /**
     * Supprimer un travail spécifique (Option)
     */
    public function deleteTravail($id)
    {
        \App\Models\TravailTarif::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'L\'option de travail a été supprimée.');
    }

    /**
     * Supprimer une épaisseur complète (ex: supprimer tout le 2cm)
     */
    public function deleteEpaisseur($ep)
    {
        // Supprime toutes les finitions pour Particuliers ET Entreprises pour cette épaisseur
        \App\Models\Tarif::where('epaisseur', $ep)->delete();
        return redirect()->back()->with('success', "L'épaisseur {$ep}cm a été retirée de tous les tarifs.");
    }
}
