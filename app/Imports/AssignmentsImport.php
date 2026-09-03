<?php

namespace App\Imports;

use App\Models\Assignment;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class AssignmentsImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }

    // --- FUNGSI PARSE TANGGAL "ANTI MELESET" (Sudah Benar) ---
    private function parseTanggal($rawTanggal)
    {
        // 1. Cek Kosong
        if (empty($rawTanggal) || $rawTanggal == 'nan' || trim($rawTanggal) == '-' || trim($rawTanggal) == '') {
            return null;
        }

        $rawTanggal = trim($rawTanggal);

        try {
            // 2. KASUS: FORMAT EXCEL ANGKA (45321)
            if (is_numeric($rawTanggal)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawTanggal);
            }

            // 3. KASUS: FORMAT INDONESIA (30 Januari 2025) -> INI PENTING!
            if (preg_match('/^(\d{1,2})\s+([a-zA-Z]+)\s+(\d{4})$/', $rawTanggal, $matches)) {
                $bulanIndo = [
                    'januari' => 'January', 'februari' => 'February', 'maret' => 'March',
                    'april' => 'April', 'mei' => 'May', 'juni' => 'June',
                    'juli' => 'July', 'agustus' => 'August', 'september' => 'September',
                    'oktober' => 'October', 'november' => 'November', 'desember' => 'December',
                    'jan' => 'Jan', 'feb' => 'Feb', 'mar' => 'Mar', 'apr' => 'Apr',
                    'mei' => 'May', 'jun' => 'Jun', 'jul' => 'Jul', 'agu' => 'Aug',
                    'sep' => 'Sep', 'okt' => 'Oct', 'nov' => 'Nov', 'des' => 'Dec'
                ];

                $tgl = $matches[1];
                $bln = strtolower($matches[2]);
                $thn = $matches[3];

                if (isset($bulanIndo[$bln])) {
                    return Carbon::parse("$tgl " . $bulanIndo[$bln] . " $thn");
                }
            }

            // 4. KASUS: RANGE TANGGAL (27-29/12/2025)
            if (preg_match('/^(\d{1,2})-(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $rawTanggal, $matches)) {
                return Carbon::create($matches[4], $matches[3], $matches[1]);
            }

            // 5. KASUS: FORMAT GARIS MIRING (15/01/2025)
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $rawTanggal)) {
                return Carbon::createFromFormat('d/m/Y', $rawTanggal);
            }

            // 6. Fallback
            return Carbon::parse($rawTanggal);

        } catch (\Exception $e) {
            return null;
        }
    }

    // --- FUNGSI MODEL (SUDAH DIPERBAIKI KOLOMNYA) ---
    // --- FUNGSI IMPORT ANTI DOUBLE (UPSERT) 🛡️ ---
    public function model(array $row)
    {
        $rawNames = $row[1] ?? null;
        if (empty($rawNames)) return null;

        $tanggal  = $this->parseTanggal($row[2] ?? null);
        $kegiatan = $row[3] ?? '-';
        $statusRaw = strtolower($row[4] ?? '');
        $status = ($statusRaw == 'true' || $statusRaw == 'sudah' || $statusRaw == '1');
        $link = $row[5] ?? '-';

        $names = explode(',', $rawNames);

        foreach ($names as $name) {
            $cleanName = trim($name);
            $cleanName = str_replace(['"', "'"], '', $cleanName);

            if (empty($cleanName)) continue;

            // GANTI 'create' JADI 'updateOrCreate'
            Assignment::updateOrCreate(
                // 1. KUNCI UNIK (Cek berdasarkan 3 data ini)
                [
                    'nama_pegawai' => $cleanName,
                    'kegiatan'     => $kegiatan,
                    'tanggal'      => $tanggal,
                ],
                // 2. DATA YANG DIUPDATE (Kalau ketemu, update ini. Kalau gak ketemu, buat baru pakai ini)
                [
                    'status_laporan' => $status,
                    'link_bukti'     => $link
                ]
            );
        }

        return null;
    }
}
