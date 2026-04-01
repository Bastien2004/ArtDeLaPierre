<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            // On change le type de integer à double (ou decimal)
            // 'epaisseur' aura désormais 8 chiffres au total dont 2 après la virgule
            $table->decimal('epaisseur', 8, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            // En cas de retour en arrière, on repasse en integer
            $table->integer('epaisseur')->change();
        });
    }
};
