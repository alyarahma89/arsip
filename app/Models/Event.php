<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kegiatan',
        'akademi',          // <--- NAH INI DIA BIANG KEROKNYA! WAJIB ADA!
        'tanggal_mulai',
        'tanggal_selesai',

        // --- DATA LINK ---
        'lokasi',           // Link Folder Laporan DTS (Lama)
        'link_pencatatan',  // Link Pencatatan Arsip Baru

        // --- DATA ORANG ---
        'penanggung_jawab',
        'panitia',

        // --- STATUS ---
        'is_archive_complete',

        // --- CHECKLIST ADMINISTRASI ---
        'adm_surat',
        'adm_dokumentasi',
        'adm_daftar_hadir',
        'adm_rundown',
        'adm_notulensi',
        'adm_laporan',
        'adm_materi_instruktur',
        'adm_materi_narasumber',
        'adm_release',
        'adm_sertifikat',
        'adm_lapgas',
    ];

    // Relasi ke Tabel Assignment (Tugas Pegawai)
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // Relasi ke Arsip (Opsional)
    public function archives()
    {
        return $this->hasMany(EventArchive::class);
    }
}
