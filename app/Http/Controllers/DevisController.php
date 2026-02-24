<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Specificite;
use Illuminate\Http\Request;

class DevisController extends Controller
{
    public function index() {
        // Groupe par Client + Heure minute pour séparer les devis faits à des moments différents
        $devisGroupes = Devis::with('specificites')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->client . $item->created_at->format('Y-m-d H:i');
            });

        return view('devis.devis', compact('devisGroupes'));
    }

    public function create()
    {
        return view('devis.create');
    }

    public function store(Request $request)
    {
        foreach ($request->lignes as $ligneData) {

            $matiere = $ligneData['longueurM'] * $ligneData['largeurM'];
            $prixPierreSeule = $matiere * $ligneData['prixM2'];

            //Calcul de la somme des spécificités pour UNE unité
            $totalSpecsUnitaire = 0;
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        $totalSpecsUnitaire += (float) ($specData['prix'] ?? 0);
                    }
                }
            }

            $prixHTFinal = ($prixPierreSeule + $totalSpecsUnitaire) * $ligneData['nombrePierre'];

            //Création de la ligne en BDD
            $devis = Devis::create([
                'client'       => $request->client,
                'adresse'      => $request->adresse,
                'typePierre'   => $ligneData['typePierre'],
                'nombrePierre' => $ligneData['nombrePierre'],
                'longueurM'    => $ligneData['longueurM'],
                'largeurM'     => $ligneData['largeurM'],
                'matiere'      => $matiere,
                'prixM2'       => $ligneData['prixM2'],
                'prixHT'       => $prixHTFinal, // Le total inclut déjà tout
            ]);

            //Enregistrement du détail des spécificités pour l'affichage
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        $devis->specificites()->create([
                            'nom'  => $specData['nom'],
                            'prix' => $specData['prix'] ?? 0,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('devis.index')->with('success', 'Devis enregistré !');
    }

    public function edit(string $id)
    {
        $devis = Devis::with('specificites')->findOrFail($id);
        return view('devis.edit', compact('devis'));
    }

    public function update(Request $request, string $id)
    {
        $devis = Devis::findOrFail($id);
        $devis->update($request->all());
        return redirect()->route('devis.index')->with('success', 'Devis mis à jour !');
    }
}
