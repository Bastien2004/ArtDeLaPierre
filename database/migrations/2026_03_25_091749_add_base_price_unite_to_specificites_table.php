<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('specificites', function (Blueprint $table) {
            $table->decimal('base_price', 10, 4)->default(0)->after('prix');
            $table->string('unite', 10)->default('u')->after('base_price');
        });
    }

    public function down()
    {
        Schema::table('specificites', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'unite']);
        });
    }
};
