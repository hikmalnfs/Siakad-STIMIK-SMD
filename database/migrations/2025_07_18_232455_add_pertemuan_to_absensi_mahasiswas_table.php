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
    Schema::table('absensi_mahasiswas', function (Blueprint $table) {
        $table->unsignedTinyInteger('pertemuan')->after('author_id');
    });
}

public function down()
{
    Schema::table('absensi_mahasiswas', function (Blueprint $table) {
        $table->dropColumn('pertemuan');
    });
}

};
