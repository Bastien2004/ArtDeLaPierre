<?php

namespace App\Http\Controllers;

use App\Models\PrixManuel;
use App\Models\StockAutre;
use App\Models\StockBloc;
use App\Models\StockCasson;
use App\Models\Stocks;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
class StocksController extends Controller
{
    // Afficher la liste
    public function index()
    {
        $stocks = Stocks::orderBy('epaisseur', 'asc')->orderBy('matiere', 'asc')->get();
        $blocs = StockBloc::orderBy('reference', 'asc')->get();
        $cassons = StockCasson::orderBy('epaisseur', 'asc')->orderBy('matiere', 'asc')->get();
        $autres = StockAutre::orderBy('matiere', 'asc')->get();
        $prixManuels = PrixManuel::orderBy('nom', 'asc')->get();
        return view('stocks.stocks', compact('stocks', 'blocs','cassons', 'autres', 'prixManuels'));
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

    public function storeCasson(Request $request)
    {
        $data = $request->validate([
            'matiere'   => 'required|string|max:255',
            'longueur'  => 'required|numeric|min:0',
            'largeur'   => 'required|numeric|min:0',
            'epaisseur' => 'required|integer|min:1',
            'quantite' =>   'required|integer|min:1',

        ]);

        StockCasson::create($data);

        return redirect()->back()->with('success', 'Casson ajouté au stock !');
    }

    public function updateCasson(Request $request, $id)
    {
        $casson = StockCasson::findOrFail($id);

        $data = $request->validate([
            'matiere'   => 'required|string|max:255',
            'longueur'  => 'required|numeric|min:0',
            'largeur'   => 'required|numeric|min:0',
            'epaisseur' => 'required|integer|min:1',
            'quantite' =>   'required|integer|min:1',
        ]);

        $casson->update($data);

        return redirect()->back()->with('success', 'Casson mis à jour !');
    }

    public function destroyCasson($id)
    {
        $casson = StockCasson::findOrFail($id);
        $casson->delete();

        return redirect()->back()->with('success', 'Casson supprimé.');
    }

    public function storeAutre(Request $request)
    {
        $data = $request->validate([
            'matiere'   => 'required|string|max:255',
            'longueur'  => 'required|numeric|min:0',
            'largeur'   => 'required|numeric|min:0',
            'epaisseur' => 'required|numeric|min:0',
            'quantite'  => 'required|integer|min:1',
            'prix_m2'   => 'required|numeric|min:0',
            'notes'     => 'nullable|string|max:500',
        ]);

        $data['nom'] = $data['matiere'];

        StockAutre::create($data);

        return redirect()->back()->with('success', 'Pierre ajoutée aux autres stocks !');
    }

    public function updateAutre(Request $request, $id)
    {
        $autre = StockAutre::findOrFail($id);

        $data = $request->validate([
            'matiere'   => 'required|string|max:255',
            'longueur'  => 'required|numeric|min:0',
            'largeur'   => 'required|numeric|min:0',
            'epaisseur' => 'required|numeric|min:0',
            'quantite'  => 'required|integer|min:1',
            'prix_m2'   => 'required|numeric|min:0',
        ]);

        $data['nom'] = $data['matiere'];

        $autre->update($data);

        return redirect()->back()->with('success', 'Stock mis à jour !');
    }

    public function destroyAutre($id)
    {
        $autre = StockAutre::findOrFail($id);
        $autre->delete();

        return redirect()->back()->with('success', 'Pierre supprimée des autres stocks.');
    }



    public function exportPdf()
    {
        $stocks = Stocks::orderBy('epaisseur', 'asc')->orderBy('matiere', 'asc')->get();
        $stocksGroupes = $stocks->groupBy('epaisseur');

        $blocs = StockBloc::orderBy('reference', 'asc')->get();
        $cassons = StockCasson::orderBy('epaisseur', 'asc')->orderBy('matiere', 'asc')->get();
        $autres = StockAutre::orderBy('matiere', 'asc')->get();

        $prixManuels = PrixManuel::orderBy('nom', 'asc')->get();

        $pdf = PDF::loadView('pdfs.stocks-template', compact(
            'stocksGroupes',
            'blocs',
            'cassons',
            'autres',
            'prixManuels'
        ));

        return $pdf->download('inventaire_art_de_la_pierre.pdf');
    }



    public function storePrixManuel(Request $request)
    {
        $data = $request->validate([
            'nom'  => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
        ]);

        PrixManuel::create($data);

        return redirect()->back()->with('success', 'Prix ajouté !');
    }

    public function updatePrixManuel(Request $request, $id)
    {
        $item = PrixManuel::findOrFail($id);
        $data = $request->validate([
            'nom'  => 'required|string|max:255',
            'prix' => 'required|numeric|min:0',
        ]);

        $item->update($data);

        return redirect()->back()->with('success', 'Prix mis à jour !');
    }

    public function destroyPrixManuel($id)
    {
        PrixManuel::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Prix supprimé.');
    }
}
