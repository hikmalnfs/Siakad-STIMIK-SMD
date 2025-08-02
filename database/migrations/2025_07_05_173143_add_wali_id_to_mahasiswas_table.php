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
    Schema::table('mahasiswas', function (Blueprint $table) {
        $table->unsignedBigInteger('wali_id')->nullable();
        $table->foreign('wali_id')->references('id')->on('dosens')->onDelete('set null');
    });
}


public function down()
{
    Schema::table('mahasiswas', function (Blueprint $table) {
        $table->dropForeign(['wali_id']);
        $table->dropColumn('wali_id');
    });
}

};
