<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->decimal('epaisseur', 8, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->integer('epaisseur')->change();
        });
    }
};
