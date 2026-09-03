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
       Schema::table('events', function (Blueprint $table) {
        // Menggunakan ENUM agar isinya TERBATAS hanya pilihan ini
        $table->enum('akademi', ['DEA', 'TA', 'VSGA', 'GTA', 'FGA', 'TIK'])
              ->nullable() // Boleh kosong (opsional)
              ->default(null)
              ->after('nama_kegiatan'); // Posisi kolom setelah nama_kegiatan
    });
}



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
        $table->dropColumn('akademi');
    });
    }
};
