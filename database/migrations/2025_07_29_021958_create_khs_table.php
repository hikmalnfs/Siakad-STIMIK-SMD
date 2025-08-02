<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('khs', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('mahasiswa_id');
        $table->unsignedBigInteger('tahun_akademik_id');
        $table->integer('semester');
        $table->float('ip_semester')->nullable();
        $table->float('ipk')->nullable();
        $table->integer('jumlah_sks')->default(0);
        $table->timestamps();

        $table->foreign('mahasiswa_id')->references('id')->on('mahasiswas')->onDelete('cascade');
        $table->foreign('tahun_akademik_id')->references('id')->on('tahun_akademiks')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('khs');
    }
};
