<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Création du compte par défaut
        User::updateOrCreate(
            ['email' => 'bastienhecquet2004@gmail.com'],
            [
                'name' => 'Administrateur',
                'password' => Hash::make('admin'),
            ]
        );
    }
}
