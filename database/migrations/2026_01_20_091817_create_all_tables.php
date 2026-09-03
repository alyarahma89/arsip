<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. TABEL USERS (User Login)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Kolom Tambahan (Role, NIP, dll)
            $table->string('role')->default('pegawai');
            $table->string('nip')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();

            $table->rememberToken();
            $table->timestamps();
        });

        // 2. TABEL EVENTS (Kegiatan DTS)
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kegiatan')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();



            // Kolom Pendukung
            $table->text('lokasi')->nullable(); // Link Drive
            $table->text('link_pencatatan')->nullable(); // Link Pencatatan Arsip (BARU)
            $table->string('penanggung_jawab')->nullable();
            $table->text('panitia')->nullable();
            $table->boolean('is_archive_complete')->default(false);

            // Checklist Administrasi
            $table->string('adm_surat')->default('BELUM');
            $table->string('adm_dokumentasi')->default('BELUM');
            $table->string('adm_daftar_hadir')->default('BELUM');
            $table->string('adm_rundown')->default('BELUM');
            $table->string('adm_notulensi')->default('BELUM');
            $table->string('adm_laporan')->default('BELUM');
            $table->string('adm_materi_instruktur')->default('BELUM');
            $table->string('adm_materi_narasumber')->default('BELUM');
            $table->string('adm_release')->default('BELUM');
            $table->string('adm_sertifikat')->default('BELUM');
            $table->string('adm_lapgas')->default('BELUM');

            $table->timestamps();
        });

        // 3. TABEL ASSIGNMENTS (Laporan Tugas Pegawai)
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pegawai')->nullable();
            $table->string('kegiatan')->nullable();
            $table->date('tanggal')->nullable();
            $table->boolean('status_laporan')->default(0);
            $table->text('link_bukti')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('events');
        Schema::dropIfExists('users');
    }
};
