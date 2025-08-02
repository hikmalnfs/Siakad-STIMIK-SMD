<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNilaiColumnsToKrsTable extends Migration
{
    public function up()
    {
        Schema::table('krs', function (Blueprint $table) {
            $table->float('nilai_angka')->nullable()->after('status');
            $table->string('nilai_huruf', 5)->nullable()->after('nilai_angka');
        });
    }

    public function down()
    {
        Schema::table('krs', function (Blueprint $table) {
            $table->dropColumn(['nilai_angka', 'nilai_huruf']);
        });
    }
}
