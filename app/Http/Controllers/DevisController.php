<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Specificite;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use Illuminate\Support\Facades\Http;

class DevisController extends Controller
{
    public function index() {
        $devisGroupes = Devis::with('specificites')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'asc')
            ->get()
            ->groupBy(function($item) {
                // L'astuce est ici : si typeClient est vide, on dit que c'est 'Entreprise'
                // pour que le groupage ne plante pas.
                $type = $item->typeClient ?? 'Entreprise';
                return $item->client . $type . $item->created_at->format('Y-m-d H:i');
            });

        $allTarifs = Tarif::all();
        $tarifsTravaux = \App\Models\TravailTarif::all();
        return view('devis.devis', compact('devisGroupes', 'tarifsTravaux', 'allTarifs'));
    }

    public function create(Request $request) {
        $clientPrefill = $request->query('client_prefill');
        $adressePrefill = $request->query('adresse_prefill');
        $timePrefill = $request->query('time_prefill');

        $referencePrefill = $request->query('reference_prefill');

        // Récupération des tarifs pour les boutons d'ajout rapide
        $tarifsTravaux = \App\Models\TravailTarif::all();
        $allTarifs = \App\Models\Tarif::all();

        $livraisonPrefill = $request->query('livraison_prefill', '0.00');
        // Si ça ne marche pas avec Email, essayez :
        $emailsCarnet = \App\Models\Email::orderBy('adresse')->get();
        $typeRaw = $request->query('type_client_prefill');
        if ($typeRaw) {
            // Si c'est dans l'URL, on l'utilise
            $typeClientPrefill = ucfirst(strtolower($typeRaw));
        } elseif (strtolower($clientPrefill) == 'particulier') {
            // Sinon, si le nom du client est "particulier", on force Particulier
            $typeClientPrefill = 'Particulier';
        } else {
            // Par défaut
            $typeClientPrefill = 'Entreprise';
        }

        // AJOUT de tarifsTravaux dans le compact
        return view('devis.create', compact(
            'clientPrefill',
            'adressePrefill',
            'referencePrefill',
            'timePrefill',
            'livraisonPrefill',
            'tarifsTravaux',
            'allTarifs',
            'emailsCarnet',
            'typeClientPrefill'
        ));
    }

    public function store(Request $request)
    {
        $dateCreation = $request->force_time ? \Carbon\Carbon::parse($request->force_time) : now();
        $livraisonFixe = (float) ($request->livraison ?? 0);
        $lignesPourMake = [];
        $totalGeneralHT = 0;
        $montantPose = (float) ($request->prixPose ?? 0);

        foreach ($request->lignes as $index => $ligneData) {
            $quantite = (int) $ligneData['nombrePierre'];
            $matiereUnitaire = (float)$ligneData['longueurM'] * (float)$ligneData['largeurM'];

            // Calcul du prix de base
            $prixTotalPierres = ($matiereUnitaire * (float)$ligneData['prixM2']) * $quantite;

            $totalOptionsLigne = 0;
            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
                        $totalOptionsLigne += (float) $specData['prix'];
                    }
                }
            }

            $prixHTFinalLigne = $prixTotalPierres + $totalOptionsLigne;
            $totalGeneralHT += $prixHTFinalLigne;

            $isLinteau = isset($ligneData['is_linteau']) && $ligneData['is_linteau'] === 'on';
            $typeLinteau = $isLinteau ? ($ligneData['type_linteau'] ?? 'lisse_adoucie') : null;
            $finition = !$isLinteau ? ($ligneData['finition'] ?? '') : null;

            // Sauvegarde en Base de Données
            $devis = new Devis([
                'client'       => $request->client,
                'reference'    => $request->reference,
                'typeClient'   => $request->type_client_global,
                'adresse'      => $request->adresse ?? '',
                'typePierre'   => $ligneData['typePierre'] ?? '',
                'is_linteau'   => $isLinteau,
                'type_linteau' => $typeLinteau,
                'finition'     => $finition,
                'epaisseur'    => $ligneData['epaisseur'] ?? 2,
                'nombrePierre' => $quantite,
                'longueurM'    => $ligneData['longueurM'],
                'largeurM'     => $ligneData['largeurM'],
                'matiere'      => $matiereUnitaire,
                'poids'        => $ligneData['poids'] ?? 0,
                'prixM2'       => $ligneData['prixM2'],
                'prixHT'       => $prixHTFinalLigne,
                'livraison'    => $livraisonFixe,
                'prixPose'     => $montantPose,
                'datefindevis' => $request->datefindevis,
            ]);

            $devis->created_at = $dateCreation;
            $devis->save();

            if (isset($ligneData['specs'])) {
                foreach ($ligneData['specs'] as $specData) {
                    if (!empty($specData['nom'])) {
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

        if (!empty($request->email_destinataire)) {
            \App\Models\Email::firstOrCreate(['adresse' => $request->email_destinataire]);
        }

        return redirect()->route('devis.index')->with('success', 'Devis généré et envoyé à Tiime !');
    }

    public function edit(string $id)
    {
        $devis = Devis::with('specificites')->findOrFail($id);
        $allTarifs = \App\Models\Tarif::all();
        return view('devis.edit', compact('devis', 'allTarifs'));
    }

    public function update(Request $request, string $id)
    {
        $devis = Devis::findOrFail($id);

        $devis->specificites()->delete();
        $totalOptionsCumulees = 0;

        if ($request->has('specs') && is_array($request->specs)) {
            foreach ($request->specs as $specData) {
                if (!empty($specData['nom'])) {
                    $devis->specificites()->create([
                        'nom' => $specData['nom'],
                        'prix' => $specData['prix'] ?? 0,
                        'base_price' => (float) ($specData['base_price'] ?? 0),
                        'unite'      => $specData['unite'] ?? 'u',


                    ]);
                    // On additionne le prix tel quel (car le JS l'a déjà multiplié par la quantité)
                    $totalOptionsCumulees += (float) ($specData['prix'] ?? 0);
                }
            }
        }

        $quantite = (int) $request->nombrePierre;
        $matiereParPierre = (float) $request->longueurM * (float) $request->largeurM;
        $prixManuelUnitaire = (float) $request->prix_manuel_unitaire;

        // GESTION DU LINTEAU
        $isLinteau = $request->has('is_linteau') && $request->is_linteau === 'on';
        $typeLinteau = $isLinteau ? ($request->type_linteau ?? 'lisse_adoucie') : null;
        $finition = !$isLinteau ? ($request->finition ?? '') : null;

        if ($prixManuelUnitaire > 0) {
            // Prix saisi manuellement : on prend directement unitaire × quantité
            $prixTotalPierres = $prixManuelUnitaire * $quantite;
        } else {
            // Calcul automatique habituel
            $prixTotalPierres = ($matiereParPierre * (float) $request->prixM2) * $quantite;
        }
        $prixHTFinal = $prixTotalPierres + $totalOptionsCumulees;

        $devis->update([
            'typePierre'   => $request->typePierre,
            'reference'    => $request->reference,
            'is_linteau'   => $isLinteau,
            'type_linteau' => $typeLinteau,
            'finition'     => $finition,
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

    public function updateGroupe(Request $request) {
        $request->validate([
            'old_client' => 'required',
            'old_date'   => 'required',
            'new_client' => 'required',
            'new_adresse'=> 'nullable',
            'new_date'   => 'nullable',
            'new_reference' => 'nullable',
        ]);

        // On écrase avec la valeur saisie, même si c'est vide (null)
        \App\Models\Devis::where('client', $request->old_client)
            ->where('created_at', $request->old_date)
            ->update([
                'client'       => $request->new_client,
                'reference' => $request->new_reference,
                'adresse'      => $request->new_adresse ?? '',
                'datefindevis' => $request->new_date,
            ]);

        return redirect()->back()->with('success', 'Mise à jour réussie');
    }


    public function updateLivraison(Request $request)
    {
        // On met à jour TOUTES les lignes avec le même montant
        Devis::where('client', $request->client)
            ->whereDate('created_at', $request->date)
            ->update([
                'livraison' => (float) $request->montant,
                'prixPose' => (float) $request->prixPose
            ]);


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
            ->orderBy('id', 'asc')
            ->get();

        if($lignes->isEmpty()) return "Aucune donnée trouvée.";

        $reference = $lignes->first()->reference ?? $request->query('ref');

        $adresse = $lignes->first()->adresse;
        $pays = $this->extrairePays($adresse);

        $totalHT = $lignes->sum('prixHT');
        $montantLivraison = $lignes->avg('livraison');
        $montantPose = $lignes->avg('prixPose');
        $totalHTAvecLivraison = $totalHT + $montantLivraison + $montantPose;

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

    public function streamPDF(Request $request, $client, $date)
    {
        $dateSql = Carbon::createFromFormat('Y-m-d-H-i-s', $date)->format('Y-m-d H:i:s');

        $lignes = Devis::where('client', $client)
            ->where('created_at', $dateSql)
            ->with('specificites')
            ->get();

        if($lignes->isEmpty()) return "Aucune donnée trouvée.";

        $pdf = PDF::loadView('pdfs.devis_template', [
            'lignes' => $lignes,
            'client' => $client,
            'adresse' => $lignes->first()->adresse,
            'pays' => $this->extrairePays($lignes->first()->adresse),
            'montantLivraison' => $lignes->avg('livraison'),
            'totalHTAvecLivraison' => $lignes->sum('prixHT') + $lignes->avg('livraison') + $lignes->avg('prixPose'),
            'date' => $lignes->first()->created_at,
            'totalHT'=> $lignes->sum('prixHT'),
            'id'=> $lignes->first()->id,
            'reference' => $request->query('ref'),
            'poids' => $lignes->sum('poids'),
        ]);

        return $pdf
            ->setOption('page-size', 'A4')
            ->setOption('margin-top', '0mm')
            ->setOption('margin-bottom', '0mm')
            ->setOption('margin-left', '0mm')
            ->setOption('margin-right', '0mm')
            ->setOption('disable-smart-shrinking', true)
            ->inline("Devis_{$client}.pdf");
    }

    public function downloadAtelierPDF(Request $request, $client, $date)
    {
        $dateSql = \Carbon\Carbon::createFromFormat('Y-m-d-H-i-s', $date)->format('Y-m-d H:i:s');

        $lignes = Devis::where('client', $client)
            ->where('created_at', $dateSql)
            ->with('specificites')
            ->orderBy('id', 'asc')
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
            $prixPose             = $lignes->avg('prixPose');
            $totalHTAvecLivraison = $totalHT + $montantLivraison + $prixPose;
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
                'reference'            => $lignes->first()->reference,
                'poids'                => $poids,
                'prixPose'             => $prixPose
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

    public function downloadCalendrierPDF(Request $request)
    {
        $mois  = $request->query('mois',  now()->month);
        $annee = $request->query('annee', now()->year);

        $livraisons = Devis::whereNotNull('datefindevis')
            ->whereMonth('datefindevis', $mois)
            ->whereYear('datefindevis',  $annee)
            ->orderBy('datefindevis')
            ->get()
            ->groupBy('datefindevis');

        $nomMois = [
            1=>'Janvier', 2=>'Février', 3=>'Mars', 4=>'Avril',
            5=>'Mai', 6=>'Juin', 7=>'Juillet', 8=>'Août',
            9=>'Septembre', 10=>'Octobre', 11=>'Novembre', 12=>'Décembre'
        ][$mois];

        $pdf = PDF::loadView('pdfs.calendrier_template', [
            'livraisons' => $livraisons,
            'mois'       => $mois,
            'annee'      => $annee,
            'nomMois'    => $nomMois,
        ]);

        return $pdf
            ->setOption('page-size', 'A4')
            ->setOption('orientation', 'Landscape')
            ->setOption('margin-top',    '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left',   '10mm')
            ->setOption('margin-right',  '10mm')
            ->setOption('disable-smart-shrinking', true)
            ->download("Calendrier_{$nomMois}_{$annee}.pdf");
    }

    public function sendToTiime(Request $request)
    {
        \Log::info('sendToTiime appelé pour: ' . $request->client);

        $request->validate(['client' => 'required', 'date' => 'required']);

        $dateMinute = \Carbon\Carbon::parse($request->date)->format('Y-m-d H:i');
        $lignes = Devis::with('specificites')
            ->where('client', $request->client)
            ->whereRaw("to_char(created_at, 'YYYY-MM-DD HH24:MI') = ?", [$dateMinute])
            ->orderBy('id', 'asc')
            ->get();

        \Log::info('Lignes trouvées: ' . $lignes->count());

        if ($lignes->isEmpty()) return response()->json(['message' => 'Aucune donnée trouvée', 'erreurs' => 1]);

        $p = $lignes->first();

        // ── Construction des lignes ───────────────────────────────────────────
        $lignesPourMake = [];

        foreach ($lignes as $devis) {
            $qte = (int)max((float)$devis->nombrePierre, 1);

            $designation = trim($devis->typePierre) ?: 'Pierre';
            if ($devis->epaisseur) $designation .= ', ' . $devis->epaisseur . 'cm';

            $dims        = number_format((float)$devis->longueurM, 2, '.', '') . 'x' . number_format((float)$devis->largeurM, 2, '.', '') . 'm';
            $description = trim($designation) . ' (' . $dims . ')';

            if ($devis->specificites->count() > 0) {
                $specsLabel = implode(' | ', $devis->specificites->map(function($spec) {
                    return $spec->nom . ' (+' . number_format($spec->prix, 2, ',', '') . '€)';
                })->toArray());
                $description .= "\n  " . $qte . ' pierre(s) - Options : ' . $specsLabel;
            } else {
                $description .= "\n  " . $qte . ' pierre(s)';
            }

            $description = substr($description, 0, 250);

            // Prix TOTAL (pas unitaire) pour éviter que Make multiplie et génère des décimales
            $prixTotal = round((float)$devis->prixHT, 2);

            if (round($prixUnitaire * $qte, 2) !== round((float)$devis->prixHT, 2)) {
                $qte = 1;
                $prixUnitaire = round((float)$devis->prixHT, 2);
            }

            $lignesPourMake[] = [
                'quantite'             => $qte,
                'libelle'              => $description,
                'item_net_price'       => $prixTotal,
                'taux_tva'             => 0.2,
                'motif_exoneration'    => 'S',
                'item_attribute_value' => 'sale',
            ];
        }

        // ── Frais de livraison ────────────────────────────────────────────────
        $livraison = round((float)$lignes->avg('livraison'), 2);
        \Log::info('Livraison: ' . $livraison);
        if ($livraison > 0) {
            $lignesPourMake[] = [
                'quantite'             => 1,
                'libelle'              => 'Frais de livraison',
                'item_net_price'       => $livraison,
                'taux_tva'             => 0.2,
                'motif_exoneration'    => 'S',
                'item_attribute_value' => 'sale',
            ];
        }

        // ── Adresse : valeurs par défaut si vides ─────────────────────────────
        $clientAdresse = !empty($p->adresse)    ? $p->adresse    : 'Non renseignée';
        $clientVille   = !empty($p->ville)      ? $p->ville      : 'Non renseignée';
        $clientCp      = !empty($p->codePostal) ? $p->codePostal : '00000';

        // ── Envoi au webhook Make ─────────────────────────────────────────────
        $payload = [
            'nom_client'     => $p->client,
            'date_emission'  => $p->created_at->format('Y-m-d'),
            'date_validite'  => (clone $p->created_at)->addDays(60)->format('Y-m-d'),
            'reference'      => $p->reference ?? '',
            'note_bas'       => "Pas d'escompte en cas de paiement anticipé. Réserve de propriété jusqu'au paiement complet.",
            'client_adresse' => $clientAdresse,
            'client_ville'   => $clientVille,
            'client_cp'      => $clientCp,
            'lignes'         => $lignesPourMake,
        ];

        \Log::info('Payload Make: ' . json_encode($payload));

        try {
            $response = Http::timeout(30)->post(
                'https://hook.eu1.make.com/8s6elwcna2gk7jv6427zdz7sfgof9dvj',
                $payload
            );

            \Log::info('Make status: ' . $response->status());
            \Log::info('Make body: ' . $response->body());

            if ($response->successful()) {
                return response()->json(['message' => 'Devis envoyé à Make avec succès ✓', 'erreurs' => 0]);
            }

            return response()->json([
                'message' => 'Erreur envoi Make: ' . $response->body(),
                'erreurs' => 1
            ], 500);

        } catch (\Exception $e) {
            \Log::error('sendToTiime exception: ' . $e->getMessage());
            return response()->json(['message' => 'Exception: ' . $e->getMessage(), 'erreurs' => 1], 500);
        }
    }

}
