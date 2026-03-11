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
        DB::statement('ALTER TABLE devis ALTER COLUMN "nombrePierre" TYPE integer USING "nombrePierre"::integer');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE devis ALTER COLUMN "nombrePierre" TYPE float USING "nombrePierre"::float');
    }
};
