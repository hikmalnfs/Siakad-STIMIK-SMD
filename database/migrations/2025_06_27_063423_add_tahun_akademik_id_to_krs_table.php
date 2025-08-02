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
        Schema::table('krs', function (Blueprint $table) {
            // Menambahkan kolom tahun_akademik_id jika belum ada
            if (!Schema::hasColumn('krs', 'tahun_akademik_id')) {
                $table->unsignedBigInteger('tahun_akademik_id')->nullable()->after('jadwal_id');
                $table->foreign('tahun_akademik_id')->references('id')->on('tahun_akademiks')->onDelete('set null');
            }

            // Pastikan kolom nilai_angka tidak ditambahkan jika sudah ada
            if (!Schema::hasColumn('krs', 'nilai_angka')) {
                $table->decimal('nilai_angka', 5, 2)->nullable()->after('status');
            }

            // Pastikan kolom nilai_huruf tidak ditambahkan jika sudah ada
            if (!Schema::hasColumn('krs', 'nilai_huruf')) {
                $table->string('nilai_huruf', 2)->nullable()->after('nilai_angka');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('krs', function (Blueprint $table) {
            // Menghapus kolom yang ditambahkan
            $table->dropForeign(['tahun_akademik_id']);
            $table->dropColumn(['tahun_akademik_id', 'nilai_angka', 'nilai_huruf']);
        });
    }
};
