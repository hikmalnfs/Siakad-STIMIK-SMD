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
        Schema::table('tagihan_kuliahs', function (Blueprint $table) {
            $table->unsignedBigInteger('users_id')->nullable()->after('id');

            // Jika kamu punya tabel users dan ingin relasi:
            // $table->foreign('users_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tagihan_kuliahs', function (Blueprint $table) {
            $table->dropColumn('users_id');
        });
    }

};
