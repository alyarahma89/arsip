<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ArsipKegiatan extends Model
{
    use HasFactory;

    protected $table = 'arsip_kegiatan';
    protected $guarded = [];

    // Fitur Cerdas: Kategorisasi Arsip Otomatis
    public function getStatusArsipAttribute()
    {
        if (!$this->tanggal_mulai) return 'Tidak Diketahui';

        $tahunKegiatan = Carbon::parse($this->tanggal_mulai)->year;
        $tahunSekarang = now()->year; // Otomatis membaca tahun berjalan (2026)

        if ($tahunKegiatan == $tahunSekarang) {
            return 'Aktif'; // Tahun 2026
        } elseif ($tahunKegiatan >= ($tahunSekarang - 3) && $tahunKegiatan < $tahunSekarang) {
            return 'Inaktif'; // Tahun 2023 - 2025
        } else {
            return 'Vital'; // Tahun 2022 ke bawah
        }
    }

    // Scope untuk mempermudah Filter di Controller
    public function scopeAktif($query) {
        return $query->whereYear('tanggal_mulai', now()->year);
    }
    public function scopeInaktif($query) {
        $tahunSekarang = now()->year;
        return $query->whereYear('tanggal_mulai', '>=', $tahunSekarang - 3)
                     ->whereYear('tanggal_mulai', '<', $tahunSekarang);
    }
}
