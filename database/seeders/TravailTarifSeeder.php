<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TravailTarifSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $travaux = [
            ['nom' => 'Rejingot', 'unite' => 'ml', 'prix' => 16.00],
            ['nom' => 'Ciselage', 'unite' => 'ml', 'prix' => 18.00],
            ['nom' => 'Oreilles', 'unite' => 'unité', 'prix' => 5.00],
        ];

        foreach ($travaux as $t) {
            \App\Models\TravailTarif::create($t);
        }
    }
}
