<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Specificite;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\Http;

class DevisController extends Controller
{
    public function index() {
        // 1. Récupérer les devis groupés
        $devisGroupes = Devis::with('specificites')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->client . $item->created_at->format('Y-m-d H:i');
            });

        // 2. RÉCUPÉRER LES TARIFS (C'est ce qui manquait)
        $tarifsTravaux = \App\Models\TravailTarif::all();

        // 3. Envoyer les deux variables à la vue
        return view('devis.devis', compact('devisGroupes', 'tarifsTravaux'));
    }

    public function create(Request $request) {
        $clientPrefill = $request->query('client_prefill');
        $adressePrefill = $request->query('adresse_prefill');
        $timePrefill = $request->query('time_prefill');

        // Récupération des tarifs pour les boutons d'ajout rapide
        $tarifsTravaux = \App\Models\TravailTarif::all();
        $allTarifs = \App\Models\Tarif::all();

        $livraisonPrefill = $request->query('livraison_prefill', '0.00');


        // AJOUT de tarifsTravaux dans le compact
        return view('devis.create', compact(
            'clientPrefill',
            'adressePrefill',
            'timePrefill',
            'livraisonPrefill',
            'tarifsTravaux',
            'allTarifs'
        ));    }

    public function store(Request $request)
    {
        // On définit la date (soit forcée, soit maintenant)
        $dateCreation = $request->force_time ? \Carbon\Carbon::parse($request->force_time) : now();

        // On récupère la livraison globale une seule fois
        $livraisonFixe = (float) ($request->livraison ?? 0);


        foreach ($request->lignes as $index => $ligneData) {
            $quantite = (int) $ligneData['nombrePierre'];
            $matiereParPierre = $ligneData['longueurM'] * $ligneData['largeurM'];

            // 1. Calcul du prix de la pierre seule (pour UNE unité)
            $prixPierreUnitaire = $matiereParPierre * $ligneData['prixM2'];

            // 2. Calcul des options (pour UNE unité)
            $totalOptionsUnitaire = 0;
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        // On additionne le prix unitaire de l'option
                        $totalOptionsUnitaire += (float) $specData['prix'];
                    }
                }
            }

            // 3. Calcul du Total HT de la ligne ( (Pierre + Options) * Quantité )
            $prixHTFinal = ($prixPierreUnitaire + $totalOptionsUnitaire) * $quantite;

            // 4. Création du devis
            $devis = new Devis([
                'client'       => $request->client,
                'adresse'      => $request->adresse ?? '',
                'typePierre'   => $ligneData['typePierre'] ?? 'Pierre Bleue',
                'epaisseur'    => $ligneData['epaisseur'] ?? 2,
                'nombrePierre' => $quantite,
                'longueurM'    => $ligneData['longueurM'],
                'largeurM'     => $ligneData['largeurM'],
                'matiere'      => $matiereParPierre,
                'prixM2'       => $ligneData['prixM2'],
                'prixHT'       => $prixHTFinal,
                'livraison'    => $livraisonFixe
            ]);

            $devis->created_at = $dateCreation;
            $devis->save();

            // 5. Sauvegarde des spécificités liées
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        $devis->specificites()->create([
                            'nom'  => $specData['nom'],
                            'prix' => $specData['prix'], // Prix unitaire
                        ]);
                    }
                }
            }
        }

        return redirect()->route('devis.index')->with('success', 'Devis généré avec succès !');
    }
    public function edit(string $id)
    {
        $devis = Devis::with('specificites')->findOrFail($id);
        return view('devis.edit', compact('devis'));
    }

    public function update(Request $request, string $id)
    {
        $devis = Devis::findOrFail($id);

        // 1. Mise à jour des spécificités (on vide et on recrée)
        $devis->specificites()->delete();

        $totalOptionsUnitaire = 0;
        if ($request->has('specs')) {
            foreach ($request->specs as $specData) {
                if (!empty($specData['nom'])) {
                    $devis->specificites()->create([
                        'nom' => $specData['nom'],
                        'prix' => $specData['prix'] ?? 0
                    ]);
                    // Somme des options pour recalculer le prixHT de la ligne
                    $totalOptionsUnitaire += (float) ($specData['prix'] ?? 0);
                }
            }
        }

        // 2. Recalcul des mesures
        $quantite = (int) $request->nombrePierre;
        $matiereParPierre = (float) $request->longueurM * (float) $request->largeurM;
        $prixPierreUnitaire = $matiereParPierre * (float) $request->prixM2;

        // 3. Calcul du Total HT Final (Indispensable pour corriger ton ancien bug)
        $prixHTFinal = ($prixPierreUnitaire + $totalOptionsUnitaire) * $quantite;

        // 4. Mise à jour de la ligne
        $devis->update([
            'typePierre'   => $request->typePierre,
            'epaisseur'    => $request->epaisseur ?? $devis->epaisseur,
            'nombrePierre' => $quantite,
            'longueurM'    => $request->longueurM,
            'largeurM'     => $request->largeurM,
            'prixM2'       => $request->prixM2,
            'matiere'      => $matiereParPierre,
            'prixHT'       => $prixHTFinal,
        ]);

        return redirect()->route('devis.index')->with('success', 'Ligne mise à jour avec succès !');
    }

    public function updateLivraison(Request $request)
    {
        // On met à jour TOUTES les lignes avec le même montant
        Devis::where('client', $request->client)
            ->where('created_at', $request->date)
            ->update(['livraison' => (float)$request->montant]);

        return redirect()->back()->with('success', 'Frais de livraison mis à jour !');
    }

    public function destroy($id)
    {
        $devis = Devis::findOrFail($id);
        $devis->specificites()->delete();
        $devis->delete();
        return redirect()->route('devis.index')->with('success', 'La ligne a été supprimée avec succès.');
    }
    private function extrairePays($adresseBrute)
    {
        try {
            // Utilisation de l'API OpenStreetMap (Nominatim)
            // Elle est excellente pour détecter les pays dans n'importe quel texte
            $response = Http::withHeaders([
                'User-Agent' => 'ArtDeLaPierreApp' // Requis par leur politique d'utilisation
            ])->get("https://nominatim.openstreetmap.org/search", [
                'q' => $adresseBrute,
                'format' => 'json',
                'addressdetails' => 1,
                'limit' => 1
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                // On récupère le nom du pays
                return $data['address']['country'] ?? 'FRANCE';
            }
        } catch (\Exception $e) {
            // En cas d'erreur réseau
        }

        return '';
    }


    public function downloadPDF(Request $request,$client, $date)
    {
        $reference = $request->query('ref');
        // On reformate la date reçue de l'URL pour la requête SQL
        // L'URL aura un format 2026-02-26-13-30-00
        $dateSql = Carbon::createFromFormat('Y-m-d-H-i-s', $date)->format('Y-m-d H:i:s');

        $lignes = Devis::where('client', $client)
            ->where('created_at', $dateSql)
            ->with('specificites')
            ->get();

        if($lignes->isEmpty()) return "Aucune donnée trouvée.";

        $adresse = $lignes->first()->adresse;
        $pays = $this->extrairePays($adresse);

        $totalHT = $lignes->sum('prixHT');
        $montantLivraison = $lignes->avg('livraison');
        $totalHTAvecLivraison = $totalHT + $montantLivraison;

        // On charge la vue qu'on va créer après
        $pdf = PDF::loadView('pdfs.devis_template', [
            'lignes' => $lignes,
            'client' => $client,
            'adresse' => $adresse,
            'pays' => $pays,
            'montantLivraison' => $montantLivraison,
            'totalHTAvecLivraison' => $totalHTAvecLivraison,
            'date'   => $lignes->first()->created_at,
            'totalHT'=> $totalHT,
            'id'=> $lignes->first()->id,
            'reference' => $reference
        ]);

        // Configuration millimétrée
        return $pdf
            ->setOption('page-size', 'A4')
            ->setOption('margin-top', '0mm')
            ->setOption('margin-bottom', '0mm')
            ->setOption('margin-left', '0mm')
            ->setOption('margin-right', '0mm')
            ->setOption('disable-smart-shrinking', true)
            ->download("Devis_{$client}.pdf");
    }
}
