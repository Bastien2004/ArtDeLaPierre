<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use Illuminate\Http\Request;

class DevisController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devis = \App\Models\Devis::all();
        return view('devis.devis', compact('devis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('devis.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validation stricte de toutes les colonnes de ta table
        $validatedData = $request->validate([
            'client'       => 'required|string|max:255',
            'adresse'      => 'nullable|string|max:255',
            'typePierre'   => 'required|string',
            'longueurCM'   => 'required|numeric|min:0',
            'largeurCM'    => 'required|numeric|min:0',
            'epaisseurCM'  => 'required|numeric|min:0',
            'matiere'      => 'required|numeric|min:0',
            'prixM2'       => 'required|numeric|min:0',
            'rejingotML'   => 'required|numeric|min:0',
            'oreilles'     => 'required|numeric|min:0',
        ]);

        // 2. Création du devis en base de données
        // On utilise $validatedData pour être sûr de n'envoyer que ce qui est propre
        Devis::create($validatedData);

        // 3. Redirection avec un message de succès
        return redirect()->route('devis.index')
            ->with('success', 'Le devis pour ' . $request->client . ' a été créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $devis = Devis::findOrFail($id);
        return view('devis.edit', compact('devis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $devis = \App\Models\Devis::findOrFail($id);
        $devis->update($request->all());
        return redirect()->route('devis.index')->with('success', 'Devis mis à jour !');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
