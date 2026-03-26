<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_autres', function (Blueprint $table) {
            $table->id();
            $table->string('nom');           // Nom / désignation de la pierre
            $table->string('matiere');       // Type de matière
            $table->decimal('longueur', 8, 2); // en mètres
            $table->decimal('largeur', 8, 2);  // en mètres
            $table->decimal('epaisseur', 8, 2);// en cm
            $table->integer('quantite')->default(1);
            $table->decimal('prix_m2', 10, 2); // Prix d'achat à l'entrée (€/m²)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_autres');
    }
};
