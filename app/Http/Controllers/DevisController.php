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
        // Groupe par Client + Date et Heure (Y-m-d H:i)
        $devisGroupes = Devis::with('specificites')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($item) {
                return $item->client . $item->created_at->format('Y-m-d H:i');
            });

        return view('devis.devis', compact('devisGroupes'));
    }

    public function create(Request $request) {
        $clientPrefill = $request->query('client_prefill');
        $adressePrefill = $request->query('adresse_prefill'); // Récupération
        $timePrefill = $request->query('time_prefill');

        return view('devis.create', compact('clientPrefill', 'adressePrefill', 'timePrefill'));
    }

    public function store(Request $request)
    {
        // On vérifie si une date est envoyée via le bouton "Ajouter une ligne"
        // Sinon, on utilise l'heure actuelle (now()) pour un vrai nouveau devis.
        $dateCreation = $request->force_time ? \Carbon\Carbon::parse($request->force_time) : now();

        foreach ($request->lignes as $ligneData) {
            $matiere = $ligneData['longueurM'] * $ligneData['largeurM'];
            $prixPierreSeule = $matiere * $ligneData['prixM2'];

            $totalSpecsUnitaire = 0;
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        $totalSpecsUnitaire += (float) ($specData['prix'] ?? 0);
                    }
                }
            }

            $prixHTFinal = ($prixPierreSeule + $totalSpecsUnitaire) * $ligneData['nombrePierre'];

            // On utilise "make" au lieu de "create" pour pouvoir manipuler les dates avant save
            $devis = new Devis([
                'client'       => $request->client,
                'adresse'      => $request->adresse ?? '', // Sécurité pour ton erreur SQLite
                'typePierre'   => $ligneData['typePierre'],
                'nombrePierre' => $ligneData['nombrePierre'],
                'longueurM'    => $ligneData['longueurM'],
                'largeurM'     => $ligneData['largeurM'],
                'matiere'      => $matiere,
                'prixM2'       => $ligneData['prixM2'],
                'prixHT'       => $prixHTFinal,
            ]);

            // FORCE l'heure pour correspondre au groupe existant
            $devis->created_at = $dateCreation;
            $devis->updated_at = $dateCreation;
            $devis->save();

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

        return redirect()->route('devis.index')->with('success', 'Ligne ajoutée au devis !');
    }

    public function edit(string $id)
    {
        $devis = Devis::with('specificites')->findOrFail($id);
        return view('devis.edit', compact('devis'));
    }

    public function update(Request $request, string $id)
    {
        $devis = Devis::findOrFail($id);

        // 1. Gérer les spécificités (On supprime tout et on recrée pour simplifier la synchro)
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
        $prixHTFinal = ($prixPierreSeule + $totalSpecsUnitaire) * $request->nombrePierre;

        // 3. Update final
        $devis->update([
            'typePierre'   => $request->typePierre,
            'nombrePierre' => $request->nombrePierre,
            'longueurM'    => $request->longueurM,
            'largeurM'     => $request->largeurM,
            'prixM2'       => $request->prixM2,
            'matiere'      => $matiere,
            'prixHT'       => $prixHTFinal,
        ]);

        return redirect()->route('devis.index')->with('success', 'Ligne et options mises à jour !');
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


    public function downloadPDF($client, $date)
    {
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

        // On charge la vue qu'on va créer après
        $pdf = PDF::loadView('pdfs.devis_template', [
            'lignes' => $lignes,
            'client' => $client,
            'adresse' => $adresse,
            'pays' => $pays,
            'date'   => $lignes->first()->created_at,
            'totalHT'=> $totalHT,
            'id'=> $lignes->first()->id
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
