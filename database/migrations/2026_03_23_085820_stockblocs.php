<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stockblocs', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->string('matiere')->default('Pierre Bleue');
            $table->decimal('hauteur', 8, 2);   // cm
            $table->decimal('largeur', 8, 2);   // cm
            $table->decimal('longueur', 8, 2);  // cm
            $table->decimal('poids', 8, 3);     // tonnes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stockblocs');
    }
};
