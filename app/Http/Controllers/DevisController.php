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

        // AJOUT de tarifsTravaux dans le compact
        return view('devis.create', compact(
            'clientPrefill',
            'adressePrefill',
            'timePrefill',
            'tarifsTravaux',
            'allTarifs'
        ));    }

    public function store(Request $request)
    {
        $dateCreation = $request->force_time ? \Carbon\Carbon::parse($request->force_time) : now();

        foreach ($request->lignes as $index => $ligneData){
            $quantite = (int) $ligneData['nombrePierre'];
            $matiere = $ligneData['longueurM'] * $ligneData['largeurM'];

            // 1. Prix de la pierre brute
            $prixPierreSeule = $matiere * $ligneData['prixM2'];

            // 2. Calcul des options
            $totalSpecsPourToutesLesPierres = 0;
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        // Chaque option est multipliée par le nombre de pierres
                        // (Ex: 2 pierres = 2 rejingots calculés ou 2 paires d'oreilles)
                        $totalSpecsPourToutesLesPierres += (float)$specData['prix'] * $quantite;
                    }
                }
            }

            // 3. Total Final HT
            $prixHTFinal = ($prixPierreSeule * $quantite) + $totalSpecsPourToutesLesPierres;

            $devis = new Devis([
                'client'       => $request->client,
                'adresse'      => $request->adresse ?? '',
                'typePierre'   => $ligneData['typePierre'],
                'epaisseur'    => $ligneData['epaisseur'] ?? 2,
                'nombrePierre' => $quantite,
                'longueurM'    => $ligneData['longueurM'],
                'largeurM'     => $ligneData['largeurM'],
                'matiere'      => $matiere,
                'prixM2'       => $ligneData['prixM2'],
                'prixHT'       => $prixHTFinal,
                'livraison'  => ($index === 0) ? (float)($request->livraison ?? 0) : 0,
            ]);

            $devis->created_at = $dateCreation;
            $devis->save();

            // Sauvegarde des spécificités individuellement pour le PDF
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    $devis->specificites()->create([
                        'nom'  => $specData['nom'],
                        'prix' => $specData['prix'], // On stocke le prix unitaire (par pierre)
                    ]);
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

        // 1. Gérer les spécificités
        $devis->specificites()->delete();

        $totalSpecsUnitaire = 0;
        if ($request->has('specs')) {
            foreach ($request->specs as $specData) {
                if (!empty($specData['nom'])) {
                    $devis->specificites()->create([
                        'nom' => $specData['nom'],
                        'prix' => $specData['prix'] ?? 0
                    ]);
                    $totalSpecsUnitaire += (float) ($specData['prix'] ?? 0);
                }
            }
        }

        // 2. Calculs
        $matiere = $request->longueurM * $request->largeurM;
        $prixPierreSeule = $matiere * $request->prixM2;

        // Le prixHT doit être calculé comme dans le store
        $prixHTFinal = ($prixPierreSeule * $request->nombrePierre) + ($totalSpecsUnitaire);

        // 3. Update final (Correction de $ligneData en $request)
        $devis->update([
            'typePierre'   => $request->typePierre,
            'epaisseur'    => $request->epaisseur ?? $devis->epaisseur, // Utilise l'ancienne si pas envoyé
            'nombrePierre' => $request->nombrePierre,
            'longueurM'    => $request->longueurM,
            'largeurM'     => $request->largeurM,
            'prixM2'       => $request->prixM2,
            'matiere'      => $matiere,
            'prixHT'       => $prixHTFinal,
            // On ne touche pas à la livraison ici pour ne pas écraser
            // celle de la première ligne si on modifie la deuxième
        ]);

        return redirect()->route('devis.index')->with('success', 'Ligne et options mises à jour !');
    }

    public function updateLivraison(Request $request)
    {
        // 1. Trouver toutes les lignes de ce devis
        $lignes = Devis::where('client', $request->client)
            ->where('created_at', $request->date)
            ->orderBy('id', 'asc')
            ->get();

        if ($lignes->count() > 0) {
            foreach ($lignes as $index => $ligne) {
                // Seule la première ligne porte le montant, les autres 0
                $montant = ($index === 0) ? (float)$request->montant : 0;
                $ligne->update(['livraison' => $montant]);
            }
        }

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
        $montantLivraison = $lignes->sum('livraison'); // Récupère la livraison stockée
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
