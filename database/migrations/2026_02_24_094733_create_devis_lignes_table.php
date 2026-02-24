<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devis_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devis_id')->constrained()->onDelete('cascade'); // Le lien magique
            $table->string('typePierre');
            $table->integer('nombrePierre');
            $table->decimal('longueurM', 8, 3);
            $table->decimal('largeurM', 8, 3);
            $table->decimal('prixM2', 10, 2);
            $table->decimal('total_ligne', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devis_lignes');
    }
};
