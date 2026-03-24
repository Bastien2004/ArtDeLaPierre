<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_cassons', function (Blueprint $table) {
            $table->id();
            $table->string('matiere');
            $table->decimal('longueur', 8, 2);  // en mètres
            $table->decimal('largeur', 8, 2);   // en mètres
            $table->integer('epaisseur');        // en cm
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_cassons');
    }
};
