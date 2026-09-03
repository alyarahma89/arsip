<?php

namespace App\Imports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas; // ✅ 1. TAMBAHAN PENTING
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// ✅ 2. Tambahkan WithCalculatedFormulas di sini 👇
class EventsImport implements OnEachRow, WithStartRow, WithCalculatedFormulas
{
    public function startRow(): int
    {
        return 3; // Mulai Baris 3
    }

    private function checkStatus($value)
    {
        if (empty($value)) return 'BELUM';
        $v = strtolower(trim($value));
        if (str_contains($v, 'tidak') || str_contains($v, 'belum') || str_contains($v, 'kurang') || $v == '-') return 'BELUM';
        if (str_contains($v, 'sudah') || str_contains($v, 'ada') || str_contains($v, 'lengkap') || $v == 'v' || $v == '1') return 'SUDAH';
        return 'BELUM';
    }

    private function parseTanggal($rawTanggal)
    {
        if (empty($rawTanggal) || $rawTanggal == 'nan') return null;
        if (is_numeric($rawTanggal)) return Date::excelToDateTimeObject($rawTanggal);

        $rawTanggal = trim($rawTanggal);
        // Format Indo: 30 Januari 2025
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
            if (isset($bulanIndo[$bln])) return Carbon::parse("$tgl " . $bulanIndo[$bln] . " $thn");
        }
        try { return Carbon::parse($rawTanggal); } catch (\Exception $e) { return null; }
    }

    // --- FUNGSI BARU: AMBIL LINK DARI RUMUS ATAU HYPERLINK ---
    private function getLinkFromCell($cell, $fallbackValue)
    {
        // 1. Cek Hyperlink Biasa (Insert > Link)
        if ($cell->hasHyperlink()) {
            return $cell->getHyperlink()->getUrl();
        }

        // 2. Cek Rumus Excel (=HYPERLINK("http...", "Text"))
        if ($cell->isFormula()) {
            $formula = $cell->getValue();

            // Ambil teks di antara tanda kutip pertama (URL-nya)
            if (preg_match('/"([^"]+)"/', $formula, $matches)) {
                $extractedUrl = $matches[1];
                if (filter_var($extractedUrl, FILTER_VALIDATE_URL)) {
                    return $extractedUrl;
                }
            }
        }

        // 3. Kalau gak ada link sama sekali, ambil teks biasa
        return $fallbackValue;
    }

    public function onRow(Row $row)
    {
        $rowIndex = $row->getIndex();
        $rowArray = $row->toArray();

        $namaKegiatan = $rowArray[1] ?? null;
        $akademi      = $rowArray[2] ?? null;

        if (empty($namaKegiatan) || $namaKegiatan == 'nan') return;

        $tglMulai   = $this->parseTanggal($rowArray[3] ?? null);
        $tglSelesai = $this->parseTanggal($rowArray[4] ?? null) ?? $tglMulai;

        // --- AMBIL LINK DENGAN CARA BARU (LEBIH CANGGIH) ---
        $worksheet = $row->getDelegate()->getWorksheet();

        // Ambil Folder DTS (Kolom S / Index 18)
        $cellDTS   = $worksheet->getCell('S' . $rowIndex);
        $linkDTS   = $this->getLinkFromCell($cellDTS, $rowArray[18] ?? '-');

        // Ambil Arsip (Kolom T / Index 19)
        $cellArsip = $worksheet->getCell('T' . $rowIndex);
        $linkArsip = $this->getLinkFromCell($cellArsip, $rowArray[19] ?? '-');

        // Simpan
        Event::updateOrCreate(
            [
                'nama_kegiatan' => $namaKegiatan,
                'tanggal_mulai' => $tglMulai,
            ],
            [
                'akademi'          => $akademi,
                'tanggal_selesai'  => $tglSelesai,
                'lokasi'           => $linkDTS,
                'link_pencatatan'  => $linkArsip,
                'penanggung_jawab' => $rowArray[16] ?? '-', // ✅ Sekarang akan berisi Nama, bukan =Q155
                'panitia'          => $rowArray[17] ?? '-',
                'adm_surat'             => $this->checkStatus($rowArray[5] ?? null),
                'adm_dokumentasi'       => $this->checkStatus($rowArray[6] ?? null),
                'adm_daftar_hadir'      => $this->checkStatus($rowArray[7] ?? null),
                'adm_rundown'           => $this->checkStatus($rowArray[8] ?? null),
                'adm_notulensi'         => $this->checkStatus($rowArray[9] ?? null),
                'adm_laporan'           => $this->checkStatus($rowArray[10] ?? null),
                'adm_materi_instruktur' => $this->checkStatus($rowArray[11] ?? null),
                'adm_materi_narasumber' => $this->checkStatus($rowArray[12] ?? null),
                'adm_release'           => $this->checkStatus($rowArray[13] ?? null),
                'adm_sertifikat'        => $this->checkStatus($rowArray[14] ?? null),
                'adm_lapgas'            => $this->checkStatus($rowArray[15] ?? null),
            ]
        );
    }
}
