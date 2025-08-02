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
    Schema::table('nilai', function (Blueprint $table) {
        $table->float('nilai_absen')->nullable();
        $table->float('nilai_tugas')->nullable();
        $table->float('nilai_uts')->nullable();
        $table->float('nilai_uas')->nullable();
        $table->float('nilai_akhir')->nullable();
        $table->string('nilai_huruf', 3)->nullable();
    });
}

public function down()
{
    Schema::table('nilai', function (Blueprint $table) {
        $table->dropColumn([
            'nilai_absen',
            'nilai_tugas',
            'nilai_uts',
            'nilai_uas',
            'nilai_akhir',
            'nilai_huruf',
        ]);
    });
}

};
