<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $finitions = [
            'Adoucie P40' => [158, 209, 256, 264], // Prix pour 2, 3, 4, 5 cm
            'Brut de sciage' => [133, 176, 216, 222],
            'Adoucie Foncé' => [187, 248, 304, 313.50],
            'Ciselé' => [187, 248, 304, 313.50],
        ];

        foreach (['Entreprise', 'Particulier'] as $type) {
            foreach ($finitions as $nom => $prix) {
                foreach ([2, 3, 4, 5] as $index => $ep) {
                    // Correction ici : 173 au lieu de $173
                    $prixFinal = ($type == 'Particulier' && $nom == 'Adoucie P40')
                        ? [173, 229, 280, 288][$index]
                        : $prix[$index];

                    \App\Models\Tarif::create([
                        'type_client' => $type,
                        'finition'    => $nom,
                        'epaisseur'   => $ep,
                        'prix_m2'     => $prixFinal
                    ]);
                }
            }
        }
    }
}
