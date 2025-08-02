<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBobotToJadwalKuliahsTable extends Migration
{
    public function up()
    {
        Schema::table('jadwal_kuliahs', function (Blueprint $table) {
            $table->integer('bobot_absen')->default(10);
            $table->integer('bobot_tugas')->default(30);
            $table->integer('bobot_uts')->default(30);
            $table->integer('bobot_uas')->default(30);
        });
    }

    public function down()
    {
        Schema::table('jadwal_kuliahs', function (Blueprint $table) {
            $table->dropColumn(['bobot_absen', 'bobot_tugas', 'bobot_uts', 'bobot_uas']);
        });
    }
}
