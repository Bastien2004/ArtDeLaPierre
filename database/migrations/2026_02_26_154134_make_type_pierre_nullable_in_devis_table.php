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
            // On ajoute ->nullable() pour autoriser le vide
            $table->string('typePierre')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->string('typePierre')->nullable(false)->change();
        });
    }
};
