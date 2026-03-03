<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PoidsPierreBleueSeeder extends Seeder
{
    public function run(): void
    {
        $donnees = [
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 2,  'poids_m2' => 54],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 3,  'poids_m2' => 81],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 4,  'poids_m2' => 108],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 5,  'poids_m2' => 135],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 6,  'poids_m2' => 162],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 8,  'poids_m2' => 216],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 10, 'poids_m2' => 270],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 12, 'poids_m2' => 324],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 15, 'poids_m2' => 405],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 16, 'poids_m2' => 432],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 18, 'poids_m2' => 486],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 20, 'poids_m2' => 540],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 22, 'poids_m2' => 594],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 24, 'poids_m2' => 648],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 25, 'poids_m2' => 675],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 28, 'poids_m2' => 756],
            ['nom' => 'Pierre Bleue', 'epaisseurCM' => 30, 'poids_m2' => 810],
        ];

        foreach ($donnees as $ligne) {
            DB::table('poids')->insert(array_merge($ligne, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
