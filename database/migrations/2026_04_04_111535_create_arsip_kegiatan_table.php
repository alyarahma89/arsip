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
        Schema::create('arsip_kegiatan', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kegiatan');
        $table->string('akademi')->nullable(); // VSGA, DEA, dll
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai')->nullable();
        $table->string('lokasi');
        $table->string('penanggung_jawab');
        $table->text('panitia')->nullable();
        $table->decimal('anggaran_operasional', 15, 2)->default(0); // Untuk Cost-Benefit AI

        // Indikator Kelengkapan Arsip (Boolean)
        $table->boolean('adm_surat')->default(false);
        $table->boolean('adm_dokumentasi')->default(false);
        $table->boolean('adm_daftar_hadir')->default(false);
        $table->boolean('adm_laporan')->default(false);

        // Untuk OCR & File
        $table->string('file_dokumen_path')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arsip_kegiatan');
    }
};
