<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalKuliahWaktuTable extends Migration
{
    public function up()
    {
        Schema::create('jadwal_kuliah_waktu', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_kuliah_id');
            $table->unsignedBigInteger('waktu_kuliah_id');
            $table->timestamps();

            // Foreign key
            $table->foreign('jadwal_kuliah_id')
                  ->references('id')->on('jadwal_kuliahs')
                  ->onDelete('cascade');

            $table->foreign('waktu_kuliah_id')
                  ->references('id')->on('waktu_kuliahs')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_kuliah_waktu');
    }
}
