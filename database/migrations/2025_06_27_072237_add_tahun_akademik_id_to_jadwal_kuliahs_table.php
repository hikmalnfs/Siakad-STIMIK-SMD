<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('jadwal_kuliahs', function (Blueprint $table) {
        $table->unsignedBigInteger('tahun_akademik_id')->nullable(); // Menambah kolom tahun_akademik_id
        $table->foreign('tahun_akademik_id')->references('id')->on('tahun_akademiks')->onDelete('cascade'); // Menambahkan foreign key constraint (jika diperlukan)
    });
}

public function down()
{
    Schema::table('jadwal_kuliahs', function (Blueprint $table) {
        $table->dropForeign(['tahun_akademik_id']); // Menghapus foreign key jika ada
        $table->dropColumn('tahun_akademik_id');
    });
}

};
