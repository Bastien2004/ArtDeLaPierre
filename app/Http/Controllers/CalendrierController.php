<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class CalendrierController extends Controller
{
    public function index()
    {
        $livraisons = DB::table('devis')
            ->whereNotNull('datefindevis')
            ->whereNotNull('client')
            ->select([
                DB::raw('MIN(id) as id'),
                'client',
                DB::raw('MIN(adresse) as adresse'),
                'datefindevis',
                'created_at',
                DB::raw('SUM("prixHT") + MIN(livraison) as montant_ttc'),
            ])
            ->groupBy('client', 'created_at', 'datefindevis')
            ->orderBy('datefindevis')
            ->get();

        return view('devis.calendrier', compact('livraisons'));
    }
}
