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
        Schema::table('devis', function (Blueprint $table) {
            // decimal(10,2) signifie : 10 chiffres au total, dont 2 après la virgule
            $table->decimal('prixHT', 10, 2)->nullable()->after('oreilles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            // En cas de rollback, on supprime la colonne pour revenir à l'état d'avant
            $table->dropColumn('prixHT');
        });
    }
};
