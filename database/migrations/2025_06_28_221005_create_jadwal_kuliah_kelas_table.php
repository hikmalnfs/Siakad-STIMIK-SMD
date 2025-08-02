<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalKuliahKelasTable extends Migration
{
    public function up()
    {
        Schema::create('jadwal_kuliah_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliahs')->onDelete('cascade');
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['jadwal_kuliah_id', 'kelas_id']); // optional supaya tidak duplikat
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_kuliah_kelas');
    }
}
