<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->decimal('rejingotML', 8, 2)->nullable()->change();
        });

        // PostgreSQL nécessite un USING explicite pour convertir en boolean
        DB::statement('ALTER TABLE devis ALTER COLUMN oreilles TYPE boolean USING (oreilles::int::boolean)');
        DB::statement('ALTER TABLE devis ALTER COLUMN oreilles DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->decimal('rejingotML', 8, 2)->nullable(false)->change();
            $table->boolean('oreilles')->nullable(false)->change();
        });
    }
};
