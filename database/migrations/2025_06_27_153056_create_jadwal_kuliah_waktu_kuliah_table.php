<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalKuliahWaktuKuliahTable extends Migration
{
    public function up()
    {
        Schema::create('jadwal_kuliah_waktu_kuliah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_kuliah_id')->constrained('jadwal_kuliahs')->onDelete('cascade');
            $table->foreignId('waktu_kuliah_id')->constrained('waktu_kuliahs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_kuliah_waktu_kuliah');
    }
}
