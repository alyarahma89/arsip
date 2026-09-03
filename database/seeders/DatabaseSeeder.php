<?php

namespace Database\Seeders;

use App\Models\User;
// use App\Models\Event;      <-- Dimatikan biar gak error
// use App\Models\Assignment; <-- Dimatikan biar gak error
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // ==========================================
        // 1. BUAT AKUN PENGGUNA (WAJIB ADA BIAR BISA LOGIN)
        // ==========================================

        // Akun Admin
        User::create([
            'name' => 'Admin Arsip',
            'email' => 'admin@komdigi.go.id',
            'password' => Hash::make('password'),
            'role' => 'arsiparis',
            'nip' => '199001012023011001',
            'jabatan' => 'Pengelola Arsip'
        ]);

        // Akun Pegawai 1
        User::create([
            'name' => 'Eki',
            'email' => 'eki@komdigi.go.id',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'nip' => '888888',
            'jabatan' => 'Staf IT'
        ]);

        // Akun Pegawai 2
        User::create([
            'name' => 'Delvi',
            'email' => 'delvi@komdigi.go.id',
            'password' => Hash::make('password'),
            'role' => 'pegawai',
            'nip' => '777777',
            'jabatan' => 'Staf Keuangan'
        ]);

        // Akun Security
        User::create([
            'name' => 'Pak Satpam',
            'email' => 'satpam@komdigi.go.id',
            'password' => Hash::make('password'),
            'role' => 'security',
        ]);

        // ==========================================
        // 2. DATA DUMMY (KEGIATAN & TUGAS) -> DIMATIKAN! ❌
        // ==========================================
        // Bagian ini sengaja dikomentari (garis miring) supaya database
        // KOSONG BERSIH saat di-reset. Data akan masuk lewat Import Excel saja.

        /* Event::create([
            'nama_kegiatan' => 'DEA Wilmar 2025',
            'tanggal_mulai' => '2025-12-18',
            'tanggal_selesai' => '2025-12-19',
            'lokasi' => 'Hotel Wilmar',
            'is_archive_complete' => false
        ]);

        Assignment::create([
            'nama_pegawai'   => 'Eki',
            'kegiatan'       => 'DEA Wilmar 2025',
            'tanggal'        => '2025-12-18',
            'status_laporan' => false,
            'link_bukti'     => '-'
        ]);

        Assignment::create([
            'nama_pegawai'   => 'Delvi',
            'kegiatan'       => 'DEA Wilmar 2025',
            'tanggal'        => '2025-12-18',
            'status_laporan' => true,
            'link_bukti'     => 'https://drive.google.com/...'
        ]);
        */
    }
}
