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
        Schema::create('tarifs', function (Blueprint $table) {
            $table->id();
            $table->string('type_client'); // 'Entreprise' ou 'Particulier'
            $table->string('finition');    // 'Adoucie P40', 'Ciselé', etc.
            $table->integer('epaisseur');  // 2, 3, 4, 5
            $table->decimal('prix_m2', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarifs');
    }
};
