<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Archive extends Model
{
    protected $fillable = [
        'no_urut', 'no_berkas', 'kode_klasifikasi', 'uraian_berkas',
        'kurun_waktu', 'no_isi_berkas', 'uraian_isi', 'tanggal_surat',
        'tingkat_perkembangan', 'jumlah_lembar', 'lokasi_fisik',
        'no_folder', 'masa_aktif', 'masa_inaktif',
        'klasifikasi_keamanan', 'tingkat_akses', 'status_akhir'
    ];
}
