<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('poids', function (Blueprint $table) {
            // renameColumn('ancien_nom', 'nouveau_nom')
            $table->renameColumn('poids/M2', 'poids_m2');
        });
    }

    public function down(): void
    {
        Schema::table('poids', function (Blueprint $table) {
            $table->renameColumn('poids_m2', 'poids/M2');
        });
    }
};
