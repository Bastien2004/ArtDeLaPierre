<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('travail_tarifs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');       // Rejingot, Ciselage, Oreilles
            $table->string('unite');     // 'ml' (mètre linéaire) ou 'unité'
            $table->decimal('prix', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travail_tarifs');
    }
};
