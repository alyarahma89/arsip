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
    Schema::create('archives', function (Blueprint $table) {
        $table->id();
        $table->string('no_urut')->nullable();
        $table->string('no_berkas')->nullable();
        $table->string('kode_klasifikasi'); // Contoh: LT.02.02
        $table->text('uraian_berkas'); // Uraian Informasi Berkas
        $table->year('kurun_waktu'); // Tahun

        // Detail Isi Berkas (Bisa dibuat JSON atau tabel terpisah, tapi kita simpelkan dulu)
        $table->string('no_isi_berkas')->nullable();
        $table->text('uraian_isi');
        $table->date('tanggal_surat');
        $table->string('tingkat_perkembangan'); // Asli/Tembusan
        $table->integer('jumlah_lembar')->default(1);

        // Lokasi Fisik
        $table->string('lokasi_fisik')->nullable(); // Misal: Lemari A, Rak 2
        $table->string('no_folder')->nullable();

        // Jadwal Retensi & Keamanan
        $table->integer('masa_aktif'); // Dalam hitungan tahun
        $table->integer('masa_inaktif'); // Dalam hitungan tahun
        $table->string('klasifikasi_keamanan'); // Biasa/Terbatas/Rahasia
        $table->string('tingkat_akses'); // Publik/Eselon/Internal
        $table->string('status_akhir'); // Musnah/Permanen

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
        Schema::dropIfExists('archives');
    }
};
