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
    {    // migration example
    Schema::table('krs', function (Blueprint $table) {
        $table->unsignedBigInteger('matkul_id')->nullable()->after('id');
        $table->foreign('matkul_id')->references('id')->on('mata_kuliahs')->onDelete('cascade');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('krs', function (Blueprint $table) {
            //
        });
    }
};
