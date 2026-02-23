<?php

namespace Database\Seeders;

use App\Models\Devis;
use App\Models\Supplement;
use Illuminate\Database\Seeder;

class DevisSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Création des Suppléments
        $ciselage = Supplement::create([
            'nom' => 'Ciselage',
            'prixUnitaire' => 25.50,
            'unite' => 'ML'
        ]);

        $polissage = Supplement::create([
            'nom' => 'Polissage',
            'prixUnitaire' => 45.00,
            'unite' => 'M²'
        ]);

        $gravure = Supplement::create([
            'nom' => 'Gravure sablée',
            'prixUnitaire' => 8.00,
            'unite' => 'Lettre'
        ]);

        // 2. Création de Devis de test
        $devis1 = Devis::create([
            'client' => 'H.C.',
            'adresse' => '...',
            'typePierre' => 'BP',
            'longueurCM' => 2.39,
            'largeurCM' => 0.49,
            'epaisseurCM' => 5,
            'matiere' => 1.1711,
            'prixM2' => 249,
            'rejingotML' => 16,
            'oreilles' => 5,
        ]);


        // 3. Liaison entre Devis et Suppléments (Table Pivot)
        // Le premier devis a du ciselage (2.5 ML)
        $devis1->supplements()->attach($ciselage->id, ['quantite' => 2.5]);


    }
}
