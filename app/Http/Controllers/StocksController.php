<?php

namespace App\Http\Controllers;

use App\Models\StockBloc;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
class StocksController extends Controller
{
    // Afficher la liste
    public function index()
    {
        $stocks = Stocks::orderBy('created_at', 'desc')->get();
        $blocs = StockBloc::orderBy('reference', 'asc')->get();
        return view('stocks.stocks', compact('stocks', 'blocs'));
    }

    // Ajouter ou Modifier (Le formulaire gère les deux via les routes)
    public function store(Request $request)
    {
        $data = $request->validate([
            'matiere'   => 'required|string|max:255',
            'quantite'  => 'required|integer|min:1',
            'longueur'  => 'required|numeric',
            'largeur'   => 'required|numeric',
            'epaisseur' => 'required|integer',
        ]);

        Stocks::create($data);

        return redirect()->back()->with('success', 'Pierre ajoutée au stock !');
    }

    public function update(Request $request, $id)
    {
        $stock = Stocks::findOrFail($id);

        $data = $request->validate([
            'matiere'   => 'required|string|max:255',
            'quantite'  => 'required|integer|min:1',
            'longueur'  => 'required|numeric',
            'largeur'   => 'required|numeric',
            'epaisseur' => 'required|integer',
        ]);

        $stock->update($data);

        return redirect()->back()->with('success', 'Stock mis à jour !');
    }

    // Supprimer
    public function destroy($id)
    {
        $stock = Stocks::findOrFail($id);
        $stock->delete();

        return redirect()->back()->with('success', 'Ligne supprimée.');
    }

    public function storeBloc(Request $request)
    {
        $data = $request->validate([
            'reference' => 'nullable|max:255',
            'matiere'   => 'required|string|max:255',
            'hauteur'   => 'required|numeric|min:0',
            'largeur'   => 'required|numeric|min:0',
            'longueur'  => 'required|numeric|min:0',
            'poids'     => 'required|numeric|min:0',
        ]);

        StockBloc::create($data);

        return redirect()->back()->with('success', 'Bloc ajouté au stock !');
    }

    public function updateBloc(Request $request, $id)
    {
        $bloc = StockBloc::findOrFail($id);

        $data = $request->validate([
            'reference' => 'nullable|string|max:255',
            'matiere'   => 'required|string|max:255',
            'hauteur'   => 'required|numeric|min:0',
            'largeur'   => 'required|numeric|min:0',
            'longueur'  => 'required|numeric|min:0',
            'poids'     => 'required|numeric|min:0',
        ]);

        $bloc->update($data);

        return redirect()->back()->with('success', 'Bloc mis à jour !');
    }

    public function destroyBloc($id)
    {
        $bloc = StockBloc::findOrFail($id);
        $bloc->delete();

        return redirect()->back()->with('success', 'Bloc supprimé.');
    }


    public function exportPdf()
    {
        $stocks = Stocks::orderBy('epaisseur', 'asc')->orderBy('matiere', 'asc')->get();
        $stocksGroupes = $stocks->groupBy('epaisseur');
        $pdf = PDF::loadView('pdfs.stocks-template', compact('stocksGroupes'));

        return $pdf->download('inventaire_art_de_la_pierre.pdf');
    }
}
