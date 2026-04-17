<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->boolean('is_linteau')->default(false)->after('typePierre');
            $table->string('type_linteau')->nullable()->after('is_linteau');
            $table->string('finition')->nullable()->after('type_linteau');
        });
    }

    public function down()
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn(['is_linteau', 'type_linteau', 'finition']);
        });
    }
};
