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
        $lignesPourMake = [];
        $totalGeneralHT = 0;

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

            // 1. Sauvegarde en Base de Données
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
                'prixHT'       => $prixHTFinalLigne,
                'livraison'    => $livraisonFixe,
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
        return view('devis.edit', compact('devis'));
    }

    public function update(Request $request, string $id)
    {
        $devis = Devis::findOrFail($id);

        $devis->specificites()->delete();
        $totalOptionsCumulees = 0; // On va stocker le TOTAL de toutes les options envoyées

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

    public function updateGroupe(Request $request) {
        $request->validate([
            'old_client' => 'required',
            'old_date'   => 'required',
            'new_client' => 'required',
            'new_adresse'=> 'nullable',
            'new_date'   => 'nullable'
        ]);

        // On écrase avec la valeur saisie, même si c'est vide (null)
        \App\Models\Devis::where('client', $request->old_client)
            ->where('created_at', $request->old_date)
            ->update([
                'client'       => $request->new_client,
                'adresse'      => $request->new_adresse ?? '',
                'datefindevis' => $request->new_date,
            ]);

        return redirect()->back()->with('success', 'Mise à jour réussie');
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
        $request->validate(['client' => 'required', 'date' => 'required']);

        $dateMinute = \Carbon\Carbon::parse($request->date)->format('Y-m-d H:i');
        $lignes = Devis::with('specificites')
            ->where('client', $request->client)
            ->whereRaw("to_char(created_at, 'YYYY-MM-DD HH24:MI') = ?", [$dateMinute])
            ->orderBy('id', 'asc')
            ->get();

        if ($lignes->isEmpty()) return response()->json(['message' => 'Vide', 'erreurs' => 1]);

        $p = $lignes->first();
        $lignesPourMake = [];
        $totalHT = 0;

        foreach ($lignes as $devis) {
            $qte = max((int) $devis->nombrePierre, 1);

            // Prix unitaire = prix d'UNE seule pierre (surface × prixM2)
            $prixUnitaire = round((float)$devis->longueurM * (float)$devis->largeurM * (float)$devis->prixM2, 2);

            // Libellé : type + épaisseur + noms des spécificités
            $designation = trim($devis->typePierre ?? '');
            if ($devis->epaisseur) {
                $designation .= ($designation ? ', ' : '') . $devis->epaisseur . 'cm';
            }
            if ($devis->specificites->count() > 0) {
                foreach ($devis->specificites as $spec) {
                    $designation .= ', ' . $spec->nom;
                }
            }
            // Dimensions en 2ème ligne
            $designation .= "\n"
                . number_format((float)$devis->longueurM, 2, '.', '')
                . ' x '
                . number_format((float)$devis->largeurM, 2, '.', '')
                . ' x '
                . $devis->epaisseur . 'cm'
                . ' — Qté : ' . $qte;

            // Prix total de la ligne = unitaire × qté + specs
            $prixTotalSpecs = $devis->specificites->sum('prix');
            $prixTotalLigne = ($prixUnitaire * $qte) + $prixTotalSpecs;

            $lignesPourMake[] = [
                'invoice_quantity'                      => $qte,
                'invoice_quantity_unit_of_measure_code' => 'unit',
                'invoiced_item_vat_category_code'       => 'S',
                'item_attributes'                       => [[
                    'item_attribute_name'  => 'Catégorie',
                    'item_attribute_value' => 'sale',
                ]],
                'line_vat_information' => [
                    'invoiced_item_vat_rate'          => 0.20,
                    'invoiced_item_vat_category_code' => 'S',
                ],
                'price_details' => [
                    'item_net_price' => round($prixTotalLigne / $qte, 2),
                ],
                'item_information' => [
                    'item_name'       => $designation,
                    'item_attributes' => [[
                        'item_attribute_name'  => 'Catégorie',
                        'item_attribute_value' => 'sale',
                    ]],
                ],
            ];

            $totalHT += $prixTotalLigne;
        }

        // Frais de livraison en dernière ligne
        $livraison = (float) $lignes->avg('livraison');
        if ($livraison > 0) {
            $lignesPourMake[] = [
                'invoice_quantity'                      => 1,
                'invoice_quantity_unit_of_measure_code' => 'unit',
                'invoiced_item_vat_category_code'       => 'S',
                'item_attributes'                       => [[
                    'item_attribute_name'  => 'Catégorie',
                    'item_attribute_value' => 'sale',
                ]],
                'line_vat_information' => [
                    'invoiced_item_vat_rate'          => 0.20,
                    'invoiced_item_vat_category_code' => 'S',
                ],
                'price_details' => [
                    'item_net_price' => round($livraison, 2),
                ],
                'item_information' => [
                    'item_name'       => 'Frais de livraison',
                    'item_attributes' => [[
                        'item_attribute_name'  => 'Catégorie',
                        'item_attribute_value' => 'sale',
                    ]],
                ],
            ];
            $totalHT += $livraison;
        }

        \Log::info('sendToTiime payload', [
            'client'  => $p->client,
            'date'    => $p->created_at->format('Y-m-d'),
            'totalHT' => round($totalHT, 2),
            'lignes'  => array_map(function($l) {
                return [
                    'nom'      => $l['item_information']['item_name'],
                    'qte'      => $l['invoice_quantity'],
                    'unitaire' => $l['price_details']['item_net_price'],
                    'total'    => $l['invoice_quantity'] * $l['price_details']['item_net_price'],
                ];
            }, $lignesPourMake),
        ]);

        $response = Http::post("https://hook.eu1.make.com/pyok5idwiiatf8sqk5sbuipx889mhy9c", [
            'client_nom'    => $p->client,
            'client_adresse' => $p->adresse,
            'date_emission' => $p->created_at->format('Y-m-d'),
            'total_ht'      => round($totalHT, 2),
            'lignes'        => $lignesPourMake,
            'note_bas'       => "En cas de retard de paiement, une pénalité de 3 fois le taux d'intérêt légal sera appliquée, à laquelle s'ajoutera une indemnité forfaitaire pour frais de recouvrement de 40€\nPas d'escompte en cas de paiement anticipé\nNOS MARCHANDISES RESTENT NOTRE PROPRIETE JUSQU'AU PAIEMENT TOTAL DE LA FACTURE.\nLes Pierres Bleue de Soignies peuvent comporter toutes les particularités d'aspect de la matière : noirures, limés, tâches blanches, coquillages et fossiles. Aucunes réclamations concernant ces particularités ne seront prises en considération.",
            'reference' => $request->reference ?? '',

        ]);

        if ($response->failed()) {
            \Log::error('sendToTiime HTTP error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['message' => 'Erreur envoi Make', 'erreurs' => 1]);
        }

        return response()->json(['message' => 'Envoyé à Tiime ✓', 'erreurs' => 0]);
    }


}
