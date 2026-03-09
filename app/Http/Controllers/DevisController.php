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
        $dateCreation = $request->force_time ? \Carbon\Carbon::parse($request->force_time) : now();
        $livraisonFixe = (float) ($request->livraison ?? 0);

        foreach ($request->lignes as $index => $ligneData) {
            $quantite = (int) $ligneData['nombrePierre'];
            $matiereUnitaire = (float)$ligneData['longueurM'] * (float)$ligneData['largeurM'];

            // A. Calcul du bloc Pierres (Surface x Prix x Qté)
            $prixTotalPierres = ($matiereUnitaire * (float)$ligneData['prixM2']) * $quantite;

            // B. Calcul du bloc Options (On fait juste la SOMME, le JS a déjà multiplié par Qté)
            $totalOptionsLigne = 0;
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        $totalOptionsLigne += (float) $specData['prix'];
                    }
                }
            }

            // C. TOTAL FINAL : Bloc Pierres + Bloc Options
            $prixHTFinal = $prixTotalPierres + $totalOptionsLigne;

            $devis = new Devis([
                'client'       => $request->client,
                'adresse'      => $request->adresse ?? '',
                'typePierre'   => $ligneData['typePierre'] ?? '',
                'epaisseur'    => $ligneData['epaisseur'] ?? 2,
                'nombrePierre' => $quantite,
                'longueurM'    => $ligneData['longueurM'],
                'largeurM'     => $ligneData['largeurM'],
                'matiere'      => $matiereUnitaire,
                'poids'        => $ligneData['poids'] ?? 0,
                'prixM2'       => $ligneData['prixM2'],
                'prixHT'       => $prixHTFinal,
                'livraison'    => $livraisonFixe
            ]);

            $devis->created_at = $dateCreation;
            $devis->save();

            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        $devis->specificites()->create([
                            'nom'  => $specData['nom'],
                            'prix' => $specData['prix'], // Montant total envoyé par le formulaire
                        ]);
                    }
                }
            }
        }
        return redirect()->route('devis.index')->with('success', 'Devis généré !');
    }
    public function edit(string $id)
    {
        $devis = Devis::with('specificites')->findOrFail($id);
        return view('devis.edit', compact('devis'));
    }

    public function update(Request $request, string $id)
    {
        $devis = Devis::findOrFail($id);
        $devis->specificites()->delete();

        $totalOptionsCumulees = 0; // On va stocker le TOTAL de toutes les options envoyées
        if ($request->has('specs')) {
            foreach ($request->specs as $specData) {
                if (!empty($specData['nom'])) {
                    $devis->specificites()->create([
                        'nom' => $specData['nom'],
                        'prix' => $specData['prix'] ?? 0
                    ]);
                    // On additionne le prix tel quel (car le JS l'a déjà multiplié par la quantité)
                    $totalOptionsCumulees += (float) ($specData['prix'] ?? 0);
                }
            }
        }

        $quantite = (int) $request->nombrePierre;
        $matiereParPierre = (float) $request->longueurM * (float) $request->largeurM;

        // Calcul du prix de la pierre seule (multiplié par quantité)
        $prixTotalPierres = ($matiereParPierre * (float) $request->prixM2) * $quantite;

        // LE CALCUL FINAL : On ajoute les options qui sont déjà au bon montant global
        $prixHTFinal = $prixTotalPierres + $totalOptionsCumulees;

        $devis->update([
            'typePierre'   => $request->typePierre,
            'nombrePierre' => $quantite,
            'longueurM'    => $request->longueurM,
            'largeurM'     => $request->largeurM,
            'epaisseur'    => $request->epaisseur,
            'prixM2'       => $request->prixM2,
            'matiere'      => $matiereParPierre,
            'poids'        => $request->poids,
            'prixHT'       => $prixHTFinal,
        ]);

        return redirect()->route('devis.index')->with('success', 'Ligne mise à jour !');
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

    public function downloadAtelierPDF(Request $request, $client, $date)
    {
        $dateSql = \Carbon\Carbon::createFromFormat('Y-m-d-H-i-s', $date)->format('Y-m-d H:i:s');

        $lignes = Devis::where('client', $client)
            ->where('created_at', $dateSql)
            ->with('specificites')
            ->get();

        // On récupère toutes les épaisseurs uniques présentes dans vos tarifs
        $epaiseursTarifs = \App\Models\Tarif::distinct()->pluck('epaisseur')->toArray();

        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('pdfs.atelier_template', [
            'lignes' => $lignes,
            'client' => $client,
            'date'   => $lignes->first()->created_at,
            'reference' => $request->query('ref'),
            'epaiseursTarifs' => $epaiseursTarifs // On envoie la liste à la vue
        ]);

        return $pdf->setOption('page-size', 'A4')->download("ATELIER_{$client}.pdf");
    }
}
