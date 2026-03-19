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
            ->orderBy('id', 'asc')
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
        // Si ça ne marche pas avec Email, essayez :
        $emailsCarnet = \App\Models\Email::orderBy('adresse')->get();


        // AJOUT de tarifsTravaux dans le compact
        return view('devis.create', compact(
            'clientPrefill',
            'adressePrefill',
            'timePrefill',
            'livraisonPrefill',
            'tarifsTravaux',
            'allTarifs',
            'emailsCarnet',
        ));
    }

    public function store(Request $request)
    {
        $dateCreation = $request->force_time ? \Carbon\Carbon::parse($request->force_time) : now();
        $livraisonFixe = (float) ($request->livraison ?? 0);

        foreach ($request->lignes as $index => $ligneData) {
            $quantite = (int) $ligneData['nombrePierre'];
            $matiereUnitaire = (float)$ligneData['longueurM'] * (float)$ligneData['largeurM'];

            $prixTotalPierres = ($matiereUnitaire * (float)$ligneData['prixM2']) * $quantite;

            $totalOptionsLigne = 0;
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        $totalOptionsLigne += (float) $specData['prix'];
                    }
                }
            }

            $prixHTFinal = $prixTotalPierres + $totalOptionsLigne;

            // ✅ Plus de tailleRejingot ici, ça n'appartient pas au Devis
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
                'livraison'    => $livraisonFixe,
                'datefindevis' => $request->datefindevis,
            ]);

            $devis->created_at = $dateCreation;
            $devis->save();

            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        // ✅ Construction de la taille ici, dans la bonne boucle
                        $tailleMin = $specData['tailleMin'] ?? null;
                        $tailleMax = $specData['tailleMax'] ?? null;
                        $taille = ($tailleMin !== null && $tailleMax !== null)
                            ? $tailleMin . '/' . $tailleMax . ' cm'
                            : null;

                        $devis->specificites()->create([
                            'nom'            => $specData['nom'],
                            'prix'           => $specData['prix'],
                            'tailleRejingot' => $taille,
                        ]);
                    }
                }
            }
        }

        // Enregistrer l'email si fourni
        if (!empty($request->email_destinataire)) {
            \App\Models\Email::firstOrCreate(['adresse' => $request->email_destinataire]);
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

        $poids = $lignes->sum('poids');

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
            'reference' => $reference,
            'poids' => $poids,
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



    public function sendEmail(Request $request)
    {
        $request->validate([
            'destinataire' => 'required|email',
            'objet'        => 'required|string',
            'message'      => 'required|string',
            'client'       => 'required|string',
            'date'         => 'required|string',
        ]);

        try {
            $dateMinute = \Carbon\Carbon::parse($request->date)->format('Y-m-d H:i');

            $lignes = \App\Models\Devis::where('client', $request->client)
                ->whereRaw("to_char(created_at, 'YYYY-MM-DD HH24:MI') = ?", [$dateMinute])
                ->with('specificites')
                ->get();

            \Log::info('SendEmail lignes trouvées: ' . $lignes->count() . ' pour client=' . $request->client . ' date=' . $dateMinute);

            $adresse              = $lignes->first()->adresse;
            $pays                 = $this->extrairePays($adresse);
            $totalHT              = $lignes->sum('prixHT');
            $montantLivraison     = $lignes->avg('livraison');
            $totalHTAvecLivraison = $totalHT + $montantLivraison;
            $poids                = $lignes->sum('poids');

            $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('pdfs.devis_template', [
                'lignes'               => $lignes,
                'client'               => $request->client,
                'adresse'              => $adresse,
                'pays'                 => $pays,
                'montantLivraison'     => $montantLivraison,
                'totalHTAvecLivraison' => $totalHTAvecLivraison,
                'date'                 => $lignes->first()->created_at,
                'totalHT'              => $totalHT,
                'id'                   => $lignes->first()->id,
                'reference'            => null,
                'poids'                => $poids,
            ])
                ->setOption('page-size', 'A4')
                ->setOption('margin-top', '0mm')
                ->setOption('margin-bottom', '0mm')
                ->setOption('margin-left', '0mm')
                ->setOption('margin-right', '0mm')
                ->setOption('disable-smart-shrinking', true);

            $pdfContent = $pdf->output();
            $pdfNom     = 'Devis_' . $request->client . '.pdf';

            \Mail::to($request->destinataire)
                ->send(new \App\Mail\DevisEmail(
                    $request->destinataire,
                    $request->objet,
                    $request->message,
                    $pdfContent,
                    $pdfNom
                ));

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('SendEmail error: ' . $e->getMessage());
            return response('Erreur: ' . iconv('UTF-8', 'UTF-8//IGNORE', $e->getMessage()), 500)
                ->header('Content-Type', 'text/plain');
        }
    }
}
