<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;



class ArchiveController extends Controller
{
    public function index(Request $request)
{
    // 1. Ambil semua parameter filter dari URL
    $search = $request->get('search');
    $tahun  = $request->get('tahun');
    $status = $request->get('status'); // Ini isinya kata: "Permanen", "Musnah", atau "Dinilai Kembali"

    // 2. Query ke database
    $archives = \App\Models\Archive::query()
        // --> Filter Pencarian Teks
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_klasifikasi', 'LIKE', "%{$search}%")
                  ->orWhere('uraian_berkas', 'LIKE', "%{$search}%")
                  ->orWhere('uraian_isi', 'LIKE', "%{$search}%")
                  ->orWhere('no_berkas', 'LIKE', "%{$search}%")
                  ->orWhere('no_isi_berkas', 'LIKE', "%{$search}%");
            });
        })
        // --> Filter Tahun (Kurun Waktu)
        ->when($tahun, function ($query) use ($tahun) {
            $query->where('kurun_waktu', $tahun);
        })
        // --> 🚨 PENGAMAN SUPER (Filter Status JRA)
        // Kita pakai LIKE dan cari di dua kolom sekaligus jaga-jaga kalau datanya nyasar
        ->when($status, function ($query) use ($status) {
            $query->where(function ($q) use ($status) {
                $q->where('status_akhir', 'LIKE', "%{$status}%")
                  ->orWhere('klasifikasi_keamanan', 'LIKE', "%{$status}%");
            });
        })
        ->orderBy('id', 'desc')
        ->get();

    // 3. Kirim ke view
    return view('admin.archives.index', compact('archives'));
}

    public function import(Request $request)
    {
    $request->validate(['file_excel' => 'required|mimes:xlsx,xls']);

    try {
        $file = $request->file('file_excel');
        $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
        $rows = $data[0] ?? [];

        DB::beginTransaction();

        // Variabel penampung untuk data yang di-merge (mengingat baris atas)
        $lastNoUrut = null; $lastNoBerkas = null; $lastKode = null;
        $lastUraianBerkas = null; $lastTahun = null;
        $lastLokasi = null; $lastFolder = null;

        $count = 0;

        foreach ($rows as $index => $row) {
            // 1. Skip jika baris benar-benar kosong
            if (empty($row[0]) && empty($row[6]) && empty($row[7])) continue;

            // 2. Skip jika ini adalah baris Header (tulisan "NO. URUT", dll)
            if (str_contains(strtolower((string)($row[0] ?? '')), 'no')) continue;

            // 3. LOGIKA PENGINGAT (Merge Cells)
            // Mengisi data yang kosong dari baris di atasnya
            if (!empty($row[0])) $lastNoUrut = $row[0];
            if (!empty($row[1])) $lastNoBerkas = $row[1];
            if (!empty($row[2])) $lastKode = $row[2];
            if (!empty($row[3])) $lastUraianBerkas = $row[3];
            if (!empty($row[4]) && is_numeric($row[4])) $lastTahun = $row[4];
            if (!empty($row[11])) $lastLokasi = $row[11];
            if (!empty($row[12])) $lastFolder = $row[12];

            // 4. Validasi Uraian Isi (Kalau kolom H kosong, abaikan baris ini)
            if (empty($row[7])) continue;

            // 5. Normalisasi Tanggal (Sangat Hati-hati)
            $tanggal = null;
            if (!empty($row[8]) && trim($row[8]) !== '?' && trim($row[8]) !== '-') {
                try {
                    if (is_numeric($row[8])) {
                        $tanggal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[8])->format('Y-m-d');
                    } else {
                        $cleanDate = str_replace(['/', '.'], '-', trim($row[8]));
                        $tanggal = \Carbon\Carbon::parse($cleanDate)->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    $tanggal = null; // Tetap null kalau format tidak dikenali
                }
            }

            // 6. Eksekusi Simpan
            \App\Models\Archive::create([
                'no_urut'               => $lastNoUrut,
                'no_berkas'             => $lastNoBerkas,
                'kode_klasifikasi'      => $lastKode,
                'uraian_berkas'         => $lastUraianBerkas,
                'kurun_waktu'           => $lastTahun ?? date('Y'),
                'jumlah_lembar'         => $row[5] ?? ($row[10] ?? '-'),
                'no_isi_berkas'         => $row[6] ?? null,
                'uraian_isi'            => $row[7] ?? null,
                'tanggal_surat'         => $tanggal, // Sekarang aman meskipun NULL
                'tingkat_perkembangan'  => $row[9] ?? 'Asli',
                'lokasi_fisik'          => $lastLokasi ?? 'Gdrive',
                'no_folder'             => $lastFolder,
                'masa_aktif'            => $row[13] ?? '0',
                'masa_inaktif'          => $row[14] ?? '0',
                'klasifikasi_keamanan'  => $row[15] ?? 'Biasa',
                'tingkat_akses'         => $row[16] ?? 'Terbuka',
                'status_akhir'          => $row[17] ?? '-',
            ]);
            $count++;
        }

        DB::commit();
        return redirect()->back()->with('success', "Alhamdulillah, $count data berhasil diimport lengkap dengan uraian isinya!");

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Gagal Min. Pesan: ' . $e->getMessage());
    }
    }

public function truncate()
{
    try {
        // Menghapus seluruh isi tabel archives
        \App\Models\Archive::truncate();

        return redirect()->back()->with('success', 'Berhasil, Min! Semua data Buku Induk sudah dikosongkan. Silakan upload yang baru.');
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
    }
}

public function export($type)
{
    $data = Archive::all();

    if ($type == 'excel') {
        // Jika menggunakan Laravel Excel
        // return Excel::download(new ArchivesExport, 'Buku-Induk-Arsip-2025.xlsx');

        // Versi simpel tanpa library tambahan (CSV masquerading as Excel)
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Buku-Induk-Arsip-2025.csv"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['No Urut', 'No Berkas', 'Kode', 'Uraian Berkas', 'Tahun', 'Isi', 'Tanggal']);
        foreach ($data as $row) {
            fputcsv($output, [$row->no_urut, $row->no_berkas, $row->kode_klasifikasi, $row->uraian_berkas, $row->kurun_waktu, $row->uraian_isi, $row->tanggal_surat]);
        }
        fclose($output);
        return;
    }

    if ($type == 'pdf') {
        // Untuk PDF, biasanya pakai library Barryvdh\DomPDF
        // $pdf = \PDF::loadView('admin.archives.pdf', compact('data'));
        // return $pdf->download('Buku-Induk-Arsip-2025.pdf');

        return back()->with('error', 'Fitur PDF memerlukan library DomPDF, Min!');
    }
}

public function store(Request $request)
{
    $request->validate([
        'uraian_berkas' => 'required',
        'kode_klasifikasi' => 'required',
        // Tambahkan mimes xls dan xlsx
        'foto_berkas' => 'nullable|mimes:jpeg,png,jpg,pdf,xls,xlsx|max:10240', // Max naik ke 10MB
    ]);

    try {
        $data = $request->all();

        if ($request->hasFile('foto_berkas')) {
            $file = $request->file('foto_berkas');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/archives'), $fileName);
            $data['foto_berkas'] = $fileName;
        }

        \App\Models\Archive::create($data);

        return redirect()->back()->with('success', 'Berkas (Foto/PDF/Excel) berhasil disimpan!');

    } catch (\Exception $e) {
        return back()->with('error', 'Gagal: ' . $e->getMessage());
    }
}

    public function analyzeAI(Request $request)
    {
        // 1. Pengaman: Cek apakah file benar-benar dikirim dari frontend
        if (!$request->hasFile('file')) {
            return response()->json(['status' => 'error', 'message' => 'File gak ada, Min! Cek form uploadnya.']);
        }

        try {
            // 2. Siapkan file untuk dikirim ke Gemini
            $file = $request->file('file');
            $fileData = base64_encode(file_get_contents($file));
            $mimeType = $file->getMimeType();

            // 3. Ambil API Key (Pastikan di .env sudah ada GEMINI_API_KEY=xxx)
            $apiKey = env('GEMINI_API_KEY');
            if (empty($apiKey)) {
                return response()->json(['status' => 'error', 'message' => 'API Key belum dipasang di .env Min!']);
            }

            // 4. URL Sakti yang terbukti jalan semalam di DTS
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

            // 5. Tembak API Google dengan timeout 30 detik biar gak keputus di tengah jalan
            $response = Http::timeout(30)->post($url, [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => "Tolong analisis dokumen arsip ini. Ekstrak data dan berikan jawaban HANYA dalam format JSON murni: {\"kode_klasifikasi\": \"...\", \"nama_kegiatan\": \"...\", \"perihal_surat\": \"...\", \"tanggal\": \"YYYY-MM-DD\"}. Jangan berikan teks penjelasan apapun diluar JSON."],
                            ["inline_data" => ["mime_type" => $mimeType, "data" => $fileData]]
                        ]
                    ]
                ]
            ]);

            // 6. Jika Google membalas dengan sukses (Status 200)
            if ($response->successful()) {
                $data = $response->json();

                // Ambil teks balasan AI, kasih default '{}' kalau kosong
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

                // Bersihkan kalau AI iseng nambahin tulisan ```json di awal dan akhir
                $cleanText = trim(str_replace(['```json', '```'], '', $text));
                $cleanJson = json_decode($cleanText, true);

                // Cek apakah hasil decode beneran array (valid JSON)
                if (is_array($cleanJson)) {
                    return response()->json(['status' => 'success', 'result' => $cleanJson]);
                } else {
                    return response()->json(['status' => 'error', 'message' => 'AI gagal membuat format JSON yang benar.']);
                }
            }

            // 7. Kalau URL/Model salah (misal 404), lempar error ini ke layar
            return response()->json([
                'status' => 'error',
                'message' => 'Google API Error ' . $response->status() . ': ' . $response->body()
            ]);

        } catch (\Exception $e) {
            // 8. Tangkap error lain (misal koneksi internet putus)
            return response()->json(['status' => 'error', 'message' => 'Sistem Error: ' . $e->getMessage()]);
        }
    }

    
}


