<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('archives', function (Blueprint $table) {
        // Kita tambah kolom foto_berkas setelah kolom uraian_isi
        // Kolom ini dibuat nullable supaya data lama yang dari Excel nggak error
        $table->string('foto_berkas')->nullable()->after('uraian_isi');
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('archives', function (Blueprint $table) {
        $table->dropColumn('foto_berkas');
    });
}
};
