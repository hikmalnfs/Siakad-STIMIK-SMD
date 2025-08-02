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
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id'); // FK ke mahasiswa (users)
            $table->unsignedBigInteger('jadwal_id'); // FK ke jadwal_kuliahs
            $table->string('status')->nullable(); // contoh field status
            $table->timestamps();

            $table->foreign('users_id')->references('id')->on('mahasiswas')->onDelete('cascade');
            $table->foreign('jadwal_id')->references('id')->on('jadwal_kuliahs')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('krs');
    }
};
