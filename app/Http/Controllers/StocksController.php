<?php

namespace App\Http\Controllers;

use App\Models\Stocks;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
class StocksController extends Controller
{
    // Afficher la liste
    public function index()
    {
        $stocks = Stocks::orderBy('created_at', 'desc')->get();
        return view('stocks.stocks', compact('stocks'));
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

    public function exportPdf()
    {
        // Tri par matière alphabétique, puis par épaisseur croissante
        $stocks = Stocks::orderBy('matiere', 'asc')
            ->orderBy('epaisseur', 'asc')
            ->get();

        // Groupement par matière
        $stocksGroupes = $stocks->groupBy('epaisseur');

        $pdf = PDF::loadView('pdfs.stocks-template', compact('stocksGroupes'));

        return $pdf->download('etat_stock_pierres.pdf');
    }
}
