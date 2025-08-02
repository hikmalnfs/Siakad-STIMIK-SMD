<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropWaktuKuliahIdFromJadwalKuliahsTable extends Migration
{
    public function up()
    {
        Schema::table('jadwal_kuliahs', function (Blueprint $table) {
            if (Schema::hasColumn('jadwal_kuliahs', 'waktu_kuliah_id')) {
                $table->dropColumn('waktu_kuliah_id');
            }
        });
    }

    public function down()
    {
        Schema::table('jadwal_kuliahs', function (Blueprint $table) {
            $table->unsignedBigInteger('waktu_kuliah_id')->nullable();
        });
    }
}
