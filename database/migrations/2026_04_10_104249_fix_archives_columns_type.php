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
            // Kita ubah semua yang berpotensi teks jadi string biar aman
            $table->string('jumlah_lembar')->nullable()->change();
            $table->string('kurun_waktu')->nullable()->change();
            $table->string('masa_aktif')->nullable()->change();
            $table->string('masa_inaktif')->nullable()->change();
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
            //
        });
    }
};
