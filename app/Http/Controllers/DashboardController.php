<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\Assignment;
use App\Models\User;
use App\Models\UploadHistory;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EventsImport;
use App\Imports\AssignmentsImport;
use App\Exports\PegawaiRekapExport;
use Illuminate\Support\Facades\Http;
use App\Exports\AssignmentsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\EventsExport;





class DashboardController extends Controller
{
    // ==========================================
    // 🔧 HELPER: MENGHITUNG KELENGKAPAN ADM
    // ==========================================
    private function getSumSql()
    {
        $columns = [
            'adm_surat', 'adm_dokumentasi', 'adm_daftar_hadir', 'adm_rundown',
            'adm_notulensi', 'adm_laporan', 'adm_materi_instruktur',
            'adm_materi_narasumber', 'adm_release', 'adm_sertifikat', 'adm_lapgas'
        ];

        $sqlParts = [];
        foreach ($columns as $col) {
            $sqlParts[] = "(CASE WHEN $col = 'SUDAH' THEN 1 ELSE 0 END)";
        }
        return implode(' + ', $sqlParts);
    }

    // ==========================================
    // 1. DASHBOARD UTAMA (ADMIN & PEGAWAI)
    // ==========================================
    public function index(Request $request)
{
    $user = Auth::user();

    if ($user->role == 'arsiparis') {
        // Ambil tahun dari request, default 'all' untuk "Semua Tahun"
        $selectedYear = $request->input('year', 'all');

        // Ambil daftar tahun dari database untuk dropdown
        $availableYears = Assignment::selectRaw('YEAR(tanggal) as year')
                            ->union(Event::selectRaw('YEAR(tanggal_mulai) as year'))
                            ->distinct()
                            ->orderBy('year', 'desc')
                            ->pluck('year');

        // Query Utama dengan Filter Tahun
        $queryKegiatan = Event::query();
        $queryLaporan = Assignment::query();

        if ($selectedYear !== 'all') {
            $queryKegiatan->whereYear('tanggal_mulai', $selectedYear);
            $queryLaporan->whereYear('tanggal', $selectedYear);
        }

        $totalKegiatan = $queryKegiatan->count();
        $laporanSudah  = (clone $queryLaporan)->where('status_laporan', true)->count();
        $laporanBelum  = (clone $queryLaporan)->where('status_laporan', false)->count();

        // Statistik lainnya...
        $totalPegawai = User::where('role', 'pegawai')->count();
        $sumSql = $this->getSumSql();
        $dtsIncomplete = $queryKegiatan->whereRaw("($sumSql) < 11")->count();

        $recentActivities = UploadHistory::latest()->take(5)->get();

        return view('dashboard.admin', compact(
            'totalKegiatan', 'totalPegawai', 'laporanBelum', 'laporanSudah',
            'dtsIncomplete', 'recentActivities', 'selectedYear', 'availableYears'
        ));
    }


        // --- B. JIKA PEGAWAI BIASA ---
        else {
            // 1. STATISTIK KARTU
            $totalLapgas = Assignment::where('nama_pegawai', $user->name)->count();
            $selesai = Assignment::where('nama_pegawai', $user->name)->where('status_laporan', 1)->count();
            $pending = $totalLapgas - $selesai; if($pending < 0) $pending = 0;

            $totalDTS = Event::where(function($q) use ($user) {
                $q->where('panitia', 'like', "%{$user->name}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$user->name}%");
            })->count();

            // 2. DATA NOTIFIKASI (List Tugas Pending)
            $notifTasks = Assignment::where('nama_pegawai', $user->name)
                                    ->where('status_laporan', 0)
                                    ->latest('tanggal')
                                    ->take(5)
                                    ->get();

            // 3. DATA GRAFIK & FILTER TAHUN
            $filterTahun = $request->input('tahun', date('Y'));

            // Ambil tahun yang tersedia di DB
            $availableYears = array_unique(array_merge(
                Event::selectRaw('YEAR(tanggal_mulai) as year')->pluck('year')->toArray(),
                Assignment::selectRaw('YEAR(tanggal) as year')->pluck('year')->toArray()
            ));
            rsort($availableYears);
            if(empty($availableYears)) $availableYears = [date('Y')];

            // Inisialisasi Array Bulanan
            $dtsBulanan = array_fill(1, 12, 0);
            $lapgasBulanan = array_fill(1, 12, 0);

            // Query Data DTS Bulanan
            $dtsData = Event::selectRaw('MONTH(tanggal_mulai) as bulan, COUNT(*) as total')
                ->whereYear('tanggal_mulai', $filterTahun)
                ->where(function($q) use ($user) {
                    $q->where('panitia', 'like', "%{$user->name}%")
                      ->orWhere('penanggung_jawab', 'like', "%{$user->name}%");
                })->groupBy('bulan')->pluck('total', 'bulan');

            // Query Data Lapgas Bulanan
            $lapgasData = Assignment::selectRaw('MONTH(tanggal) as bulan, COUNT(*) as total')
                ->whereYear('tanggal', $filterTahun)
                ->where('nama_pegawai', $user->name)
                ->groupBy('bulan')->pluck('total', 'bulan');

            // Isi Array
            foreach ($dtsData as $bulan => $total) $dtsBulanan[$bulan] = $total;
            foreach ($lapgasData as $bulan => $total) $lapgasBulanan[$bulan] = $total;

            return view('dashboard.pegawai', compact(
                'totalLapgas', 'selesai', 'pending', 'totalDTS',
                'dtsBulanan', 'lapgasBulanan',
                'filterTahun', 'availableYears', 'notifTasks'
            ));
        }
    }



    // ==========================================
    // 2. HALAMAN KHUSUS PEGAWAI (DTS & LAPGAS)
    // ==========================================

    public function pegawaiDts(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        // AMBIL DATA DTS
        $queryEvents = Event::where(function($q) use ($user) {
            $q->where('panitia', 'like', "%{$user->name}%")
              ->orWhere('penanggung_jawab', 'like', "%{$user->name}%");
        });

        if ($search) {
            $queryEvents->where('nama_kegiatan', 'like', "%{$search}%");
        }

        $myEvents = $queryEvents->orderBy('tanggal_mulai', 'desc')->paginate(10)->withQueryString();

        // 👇 INI YANG KURANG MIN (UNTUK NOTIFIKASI TUGAS DI HEADER)
        $myAssignments = Assignment::where('nama_pegawai', $user->name)->get();
        $pending = $myAssignments->where('status_laporan', 0)->count();

        return view('dashboard.pegawai_dts', compact('myEvents', 'myAssignments', 'pending'));
    }

    public function pegawaiLapgas(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $queryAssign = Assignment::where('nama_pegawai', 'like', "%{$user->name}%");

        if ($search) {
            $queryAssign->where('kegiatan', 'like', "%{$search}%");
        }

        $myAssignments = $queryAssign->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();

        // Data Sidebar/Notif
        $totalTugas = Assignment::where('nama_pegawai', $user->name)->count();
        $selesai = Assignment::where('nama_pegawai', $user->name)->where('status_laporan', 1)->count();
        $pending = $totalTugas - $selesai; if($pending < 0) $pending = 0;

        return view('dashboard.pegawai_lapgas', compact('myAssignments', 'pending'));
    }

    // ==========================================
    // 3. FITUR ADMIN (LIST, CRUD, ANALISIS)
    // ==========================================

    public function listEvents(Request $request)
    {
        $sumSql = $this->getSumSql();
        $query = Event::query();

        // 1. Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('akademi', 'like', "%{$search}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$search}%")
                  ->orWhere('panitia', 'like', "%{$search}%");
            });
        }

        // 2. Filter Status (Complete/Incomplete)
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'complete': $query->whereRaw("($sumSql) = 11"); break;
                case 'incomplete': $query->whereRaw("($sumSql) < 11"); break;
                case 'has-archive': $query->where(function($q) { $q->where('lokasi', '!=', '-')->orWhere('link_pencatatan', '!=', '-'); }); break;
            }
        }

        // 3. Filter Bulan & Tahun
        if ($request->month) {
            $query->whereMonth('tanggal_mulai', $request->month);
        }
        if ($request->year) {
            $query->whereYear('tanggal_mulai', $request->year);
        }

        // Filter Akademi
        if ($request->akademi) {
            $query->where('akademi', $request->akademi);
        }

        // 👇 TAMBAHAN FILTER KATEGORI ARSIP (SMART ARSIP) 👇
        if ($request->has('kategori_arsip')) {
            $tahunSekarang = now()->year; // Otomatis mendeteksi tahun berjalan (2026)

            if ($request->kategori_arsip == 'aktif') {
                // Arsip Aktif: Tahun 2026
                $query->whereYear('tanggal_mulai', $tahunSekarang);
            } elseif ($request->kategori_arsip == 'inaktif') {
                // Arsip Inaktif: 2023 - 2025
                $query->whereYear('tanggal_mulai', '>=', $tahunSekarang - 3)
                      ->whereYear('tanggal_mulai', '<', $tahunSekarang);
            } elseif ($request->kategori_arsip == 'vital') {
                // Arsip Vital: 2022 ke bawah
                $query->whereYear('tanggal_mulai', '<', $tahunSekarang - 3);
            }
        }
        // ☝️ ===============================================

        // 4. Urutkan & Paginate
        $events = $query->orderBy('tanggal_mulai', 'desc')->paginate(10)->withQueryString();

        // Data Statistik
        $totalEvents = Event::count();
        $countLengkap = Event::whereRaw("($sumSql) = 11")->count();
        $countPerluTindakan = $totalEvents - $countLengkap;
        $countArsip = Event::where(function($q) { $q->where('lokasi', '!=', '-')->orWhere('link_pencatatan', '!=', '-'); })->count();

        return view('dashboard.events', compact('events', 'totalEvents', 'countLengkap', 'countArsip', 'countPerluTindakan'));
    }

    public function createEvent() { return view('dashboard.create_event'); }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required',
            'akademi' => 'required',
            'tanggal_mulai' => 'required|date'
        ]);

        Event::create(array_merge($request->all(), [
            'tanggal_selesai' => $request->tanggal_selesai ?? $request->tanggal_mulai,
            'penanggung_jawab' => $request->penanggung_jawab ?? '-',
            'panitia' => $request->panitia ?? '-',
            'lokasi' => $request->lokasi ?? '-',
            'anggaran_operasional' => $request->anggaran_operasional ?? 0, // Ditambahkan untuk AI
            'link_pencatatan' => $request->link_pencatatan ?? '-'
        ]));

        return redirect()->route('admin.events')->with('success', 'Berhasil menambahkan kegiatan baru!');
    }

    public function editEvent($id) { return view('dashboard.edit_event', ['event' => Event::findOrFail($id)]); }

    public function updateEvent(Request $request, $id)
    {
        Event::findOrFail($id)->update($request->all());
        return redirect()->route('admin.events')->with('success', 'Data diperbarui!');
    }

    public function deleteEvent($id) { Event::findOrFail($id)->delete(); return back()->with('success', 'Data dihapus!'); }

    public function truncateEvents() { Event::truncate(); return redirect()->route('admin.events')->with('success', 'Semua data dihapus!'); }

    // ==========================================
    // 4. IMPORT & EXPORT
    // ==========================================

    public function importDTS(Request $request)
    {
        $request->validate(['file_dts' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new EventsImport, $request->file('file_dts'));
            UploadHistory::create(['file_name' => $request->file('file_dts')->getClientOriginalName(), 'type' => 'DTS', 'status' => 'Sukses', 'message' => 'Berhasil import.', 'user_name' => Auth::user()->name]);
            return back()->with('success', 'Import DTS Berhasil!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal Import: ' . $e->getMessage()); }
    }

    public function importLapgas(Request $request)
    {
        $request->validate(['file_lapgas' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new AssignmentsImport, $request->file('file_lapgas'));
            UploadHistory::create(['file_name' => $request->file('file_lapgas')->getClientOriginalName(), 'type' => 'Lapgas', 'status' => 'Sukses', 'message' => 'Berhasil import.', 'user_name' => Auth::user()->name]);
            return back()->with('success', 'Import Lapgas Berhasil!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal Import: ' . $e->getMessage()); }
    }

    public function exportRekap()
    {
        $user = Auth::user();
        $events = Event::where('panitia', 'like', "%{$user->name}%")->orWhere('penanggung_jawab', 'like', "%{$user->name}%")->orderBy('tanggal_mulai', 'desc')->get();
        $assignments = Assignment::where('nama_pegawai', 'like', "%{$user->name}%")->orderBy('tanggal', 'desc')->get();
        $namaFile = 'Rekap_Kinerja_' . str_replace(' ', '_', $user->name) . '_' . date('Ymd') . '.xlsx';
        return Excel::download(new PegawaiRekapExport($events, $assignments, $user), $namaFile);
    }

    public function assignments(Request $request)
    {
        $query = Assignment::latest();

        // Filter Status
        if ($request->filter == 'completed') $query->where('status_laporan', true);
        if ($request->filter == 'pending') $query->where('status_laporan', false);
        if ($request->filter == 'has_proof') $query->where('link_bukti', '!=', '-')->where('link_bukti', '!=', '');

        // Filter Pencarian
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_pegawai', 'like', "%{$request->search}%")
                  ->orWhere('kegiatan', 'like', "%{$request->search}%");
            });
        }

        // Filter Bulan & Tahun
        if ($request->bulan) {
            $query->whereMonth('tanggal', $request->bulan);
        }
        if ($request->tahun) {
            $query->whereYear('tanggal', $request->tahun);
        }

        $assignments = $query->paginate(20)->withQueryString();

        return view('dashboard.assignments', compact('assignments'));
    }

    public function analisis(Request $request)
    {
        $sumSql = $this->getSumSql();
        $tahun = $request->get('tahun');
        $bulan = $request->get('bulan');
        $queryEvent = Event::query(); $queryAssign = Assignment::query();

        if ($tahun) { $queryEvent->whereYear('tanggal_mulai', $tahun); $queryAssign->whereYear('tanggal', $tahun); }
        if ($bulan) { $queryEvent->whereMonth('tanggal_mulai', $bulan); $queryAssign->whereMonth('tanggal', $bulan); }

        $totalKegiatan = $queryEvent->count();
        $totalPegawai = User::where('role', 'pegawai')->count();
        $laporanSudah = (clone $queryAssign)->where('status_laporan', true)->count();
        $laporanBelum = (clone $queryAssign)->where('status_laporan', false)->count();

        $trendSudah = array_fill(1, 12, 0); $trendBelum = array_fill(1, 12, 0);
        $assignData = Assignment::select(DB::raw('MONTH(tanggal) as bulan'), 'status_laporan', DB::raw('count(*) as total'))->groupBy(DB::raw('MONTH(tanggal)'), 'status_laporan');
        if($tahun) $assignData->whereYear('tanggal', $tahun);

        foreach($assignData->get() as $d) {
            if($d->status_laporan) $trendSudah[$d->bulan] = $d->total; else $trendBelum[$d->bulan] = $d->total;
        }

        $dtsLengkap = (clone $queryEvent)->whereRaw("($sumSql) = 11")->count();
        $dtsBelum = $totalKegiatan - $dtsLengkap;
        $topPegawai = (clone $queryAssign)->where('status_laporan', true)->select('nama_pegawai', DB::raw('count(*) as total'))->groupBy('nama_pegawai')->orderByDesc('total')->limit(5)->get();

        return view('dashboard.analisis', compact('totalKegiatan', 'totalPegawai', 'laporanSudah', 'laporanBelum', 'trendSudah', 'trendBelum', 'dtsLengkap', 'dtsBelum', 'tahun', 'bulan', 'topPegawai'));
    }

    // ==========================================
    // 5. FITUR TAMBAHAN (UPLOAD, DOWNLOAD, PROFILE, HISTORY)
    // ==========================================

    public function uploadLapgas(Request $request, $id)
    {
        $request->validate(['link_bukti' => 'required|url']);
        Assignment::findOrFail($id)->update(['link_bukti' => $request->link_bukti, 'status_laporan' => true]);
        return back()->with('success', 'Laporan berhasil dikirim!');
    }

    public function downloadTemplateDTS() { return response()->download(public_path('templates/Template_DTS.xlsx')); }

    public function editProfile() { return view('dashboard.profile', ['user' => Auth::user()]); }

    public function updateProfile(Request $request)
{
    $user = User::find(Auth::id());

    $request->validate([
        'name'     => 'required|string|max:255',
        'nip'      => 'nullable|string|max:20',
        'jabatan'  => 'nullable|string|max:100',
        'email'    => 'required|email|unique:users,email,' . $user->id,
        // Naikkan ke 8 karakter biar Google gak peringatan "lemah" terus
        'password' => 'nullable|min:8|confirmed',
    ], [
        'password.confirmed' => 'Konfirmasi password nggak cocok, Min!',
        'password.min'       => 'Password minimal 8 karakter ya biar kuat!',
    ]);

    // Data dasar yang diupdate
    $data = [
        'name'    => $request->name,
        'nip'     => $request->nip,
        'jabatan' => $request->jabatan,
        'email'   => $request->email
    ];

    // Jika password diisi (Ganti password)
    if ($request->filled('password')) {
        // Pakai Hash::make lebih disarankan di Laravel terbaru
        $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
    }

    $user->update($data);

    return back()->with('success', 'Profil dan Password berhasil diperbarui! Sekarang akun kamu lebih aman. 🛡️✨');
}

    public function history()
    {
        $histories = UploadHistory::latest()->paginate(15);
        return view('dashboard.history', compact('histories'));
    }

    public function updateAssignment(Request $request, $id)
    {
        $item = Assignment::findOrFail($id);
        $item->update([
            'nama_pegawai' => $request->nama_pegawai,
            'tanggal' => $request->tanggal,
            'kegiatan' => $request->kegiatan,
            'link_bukti' => $request->link_bukti,
            'status_laporan' => $request->has('status_laporan') ? 1 : 0
        ]);
        return back()->with('success', 'Data laporan berhasil diperbarui!');
    }

    public function deleteAssignment($id)
    {
        Assignment::findOrFail($id)->delete();
        return back()->with('success', 'Data laporan berhasil dihapus!');
    }

    // ==========================================
    // 6. FUNGSI TAMBAH LAPGAS MANUAL (ADMIN)
    // ==========================================
    public function createAssignment()
    {
        $pegawaiList = User::where('role', 'pegawai')->get();
        return view('dashboard.create_assignment', compact('pegawaiList'));
    }

    public function storeAssignment(Request $request)
    {
        $request->validate([
            'nama_pegawai' => 'required|string',
            'tanggal' => 'required|date',
            'kegiatan' => 'required|string',
            'link_bukti' => 'nullable|string',
        ]);

        Assignment::create([
            'nama_pegawai' => $request->nama_pegawai,
            'tanggal' => $request->tanggal,
            'kegiatan' => $request->kegiatan,
            'link_bukti' => $request->link_bukti ?? '-',
            'status_laporan' => $request->has('status_laporan') ? 1 : 0
        ]);

        return redirect()->route('admin.assignments')->with('success', 'Laporan Tugas berhasil ditambahkan manual!');
    }

    // ==========================================
    // 🚀 FITUR AI: ASISTEN PEMBUAT LAPORAN
    // ==========================================
    public function enhanceText(Request $request)
    {
        $request->validate(['text' => 'required|string']);

        $apiKey = env('GROQ_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key Groq belum disetting di .env'], 500);
        }

        $systemPrompt = "Anda adalah asisten ahli administrasi instansi pemerintahan. Tugas Anda adalah memperbaiki teks draf laporan kegiatan menjadi bahasa Indonesia yang sangat formal, baku, rapi, dan profesional. Jangan tambahkan kalimat pengantar/penutup, langsung berikan hasil teksnya saja.";

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "Teks asli: " . $request->text]
                ],
                'temperature' => 0.7
            ]);

            if ($response->successful()) {
                $result = $response->json('choices.0.message.content');
                return response()->json(['result' => trim($result)]);
            }

            return response()->json(['error' => 'Groq API Error: ' . $response->body()], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ==========================================
    // FITUR EXPORT LAPGAS (PDF, EXCEL, CSV)
    // ==========================================
    public function exportExcelLapgas(Request $request)
    {
        return Excel::download(new AssignmentsExport($request), 'Data_Laporan_Tugas.xlsx');
    }

    public function exportCsvLapgas(Request $request)
    {
        return Excel::download(new AssignmentsExport($request), 'Data_Laporan_Tugas.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdfLapgas(Request $request)
    {
        $export = new AssignmentsExport($request);
        $assignments = $export->collection();

        $pdf = PDF::loadView('exports.lapgas_pdf', compact('assignments'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Data_Laporan_Tugas.pdf');
    }

    // ==========================================
    // FITUR EXPORT KEGIATAN DTS (PDF, EXCEL, CSV)
    // ==========================================
    public function exportExcelEvent(Request $request)
    {
        return Excel::download(new EventsExport($request), 'Data_Kegiatan_DTS.xlsx');
    }

    public function exportCsvEvent(Request $request)
    {
        return Excel::download(new EventsExport($request), 'Data_Kegiatan_DTS.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdfEvent(Request $request)
    {
        $export = new EventsExport($request);
        $events = $export->collection();

        $pdf = PDF::loadView('exports.event_pdf', compact('events'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Data_Kegiatan_DTS.pdf');
    }

    // ==========================================
    // FITUR AI: CHATBOT ARAI (NLP) UNTUK ADMIN & PEGAWAI
    // ==========================================
    // ==========================================
    // FITUR AI: CHATBOT ARAI (NLP) SUPER DETAIL & ANALITIK
    // ==========================================
    public function chatArai(Request $request)
    {
        $userMessage = $request->message;
        $user = \Illuminate\Support\Facades\Auth::user();

        // 1. DATA GLOBAL DASAR
        $totalEvents = \App\Models\Event::count();
        $totalLapgas = \App\Models\Assignment::count();
        $globalPendingLapgas = \App\Models\Assignment::where('status_laporan', false)->count();

        // 2. DATA MENDALAM (DEEP ANALYTICS) UNTUK COST-BENEFIT & PREDIKSI
        // Kita hitung statistik spesifik per Akademi (VSGA, DEA, dll)
        $statistikAkademi = \App\Models\Event::select(
            'akademi',
            \Illuminate\Support\Facades\DB::raw('count(*) as total_kegiatan'),
            \Illuminate\Support\Facades\DB::raw('SUM(anggaran_operasional) as total_anggaran')
        )->groupBy('akademi')->get()->toJson();

        // Hitung kegiatan yang administrasinya masih bolong/belum lengkap (Anggap < 11 field terisi itu bolong)
        $sumSql = $this->getSumSql();
        $kegiatanBolong = \App\Models\Event::whereRaw("($sumSql) < 11")->count();

        // 3. DATA PRIBADI USER
        $myAssignments = \App\Models\Assignment::where('nama_pegawai', $user->name)->get();
        $myPendingCount = $myAssignments->where('status_laporan', false)->count();

        // 4. PROMPT SYSTEM (MEMBERI TAHU AI CARA MENGANALISIS)
        $systemPrompt = "Nama kamu adalah ARAI, asisten AI cerdas dan Analis Data Senior di BBPSDMP Komdigi.
        User yang bertanya bernama: Kak {$user->name}.
        Gaya bahasamu asyik, profesional, dan menggunakan format yang rapi (gunakan bullet points jika perlu).

        [DATA REAL-TIME UNTUK DIANALISIS]:
        1. Total Kegiatan Instansi: {$totalEvents} kegiatan.
        2. Kegiatan dengan Administrasi Belum Lengkap: {$kegiatanBolong} kegiatan.
        3. Total Laporan Tugas Pegawai (Lapgas): {$totalLapgas} laporan (Masih Pending: {$globalPendingLapgas}).
        4. Data Kinerja & Anggaran per Akademi (JSON): {$statistikAkademi}
        5. Tugas milik Kak {$user->name} yang belum beres: {$myPendingCount} tugas.

        [INSTRUKSI WAJIB UNTUK ARAI]:
        - Jika user bertanya tentang efisiensi, cost-benefit, atau evaluasi, gunakan data JSON 'Kinerja & Anggaran per Akademi' di atas.
        - Analisis mana akademi yang anggarannya paling besar tapi mungkin secara administratif kurang tertib, atau sebaliknya.
        - Berikan insight prediktif (misal: 'Banyak administrasi yang belum lengkap (ada {$kegiatanBolong}), ini bisa menghambat pencairan anggaran berikutnya...').
        - Jangan sekadar membaca angka, berikan KESIMPULAN dan SARAN dari angka tersebut layaknya analis konsultan.
        - Jawab dengan detail tapi tetap mudah dibaca.";

        $apiKey = env('GROQ_API_KEY');

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userMessage],
                ],
                'temperature' => 0.5, // Dibuat 0.5 agar jawabannya lebih logis dan analitis
            ]);

            if ($response->successful()) {
                $result = $response->json()['choices'][0]['message']['content'];
                return response()->json(['reply' => $result]);
            } else {
                return response()->json(['reply' => 'Waduh, koneksi Arai lagi terputus nih Kak. Coba lagi ya! 🔧']);
            }
        } catch (\Exception $e) {
            return response()->json(['reply' => 'Maaf Kak, Arai sedang maintenance. Tunggu sebentar ya! 🙏']);
        }
    }

    // ==========================================
    // EXPORT LAPGAS KHUSUS PEGAWAI (Data Pribadi)
    // ==========================================
    public function exportExcelMyLapgas(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MyAssignmentsExport($request), 'Laporan_Tugas_Saya.xlsx');
    }

    public function exportCsvMyLapgas(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MyAssignmentsExport($request), 'Laporan_Tugas_Saya.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdfMyLapgas(Request $request)
    {
        $export = new \App\Exports\MyAssignmentsExport($request);
        $assignments = $export->collection();

        $pdf = \PDF::loadView('exports.my_lapgas_pdf', compact('assignments'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('Laporan_Tugas_Saya.pdf');
    }

    // ==========================================
    // EXPORT DATA DTS KHUSUS PEGAWAI
    // ==========================================
    public function exportExcelMyDts(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MyEventsExport($request), 'Data_Kegiatan_DTS.xlsx');
    }

    public function exportWordMyDts(Request $request)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\MyEventsExport($request), 'Data_Kegiatan_DTS.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdfMyDts(Request $request)
    {
        $export = new \App\Exports\MyEventsExport($request);
        $events = $export->collection();

        $pdf = \PDF::loadView('exports.my_dts_pdf', compact('events'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Data_Kegiatan_DTS.pdf');
    }

    // ==========================================
    // FITUR BARU: AUTO-GENERATE LAPORAN PDF (MAIL MERGE STYLE)
    // ==========================================
    public function cetakLaporanPdf($id)
{
    // 1. Ambil data event
    $event = \App\Models\Event::findOrFail($id);

    // 2. Definisi Label untuk kolom database
    $columns = [
        'adm_surat' => 'Surat Menyurat',
        'adm_dokumentasi' => 'Dokumentasi Kegiatan',
        'adm_daftar_hadir' => 'Daftar Hadir',
        'adm_rundown' => 'Rundown',
        'adm_notulensi' => 'Notulensi',
        'adm_laporan' => 'Laporan Kegiatan',
        'adm_materi_instruktur' => 'Materi Instruktur',
        'adm_materi_narasumber' => 'Materi Narasumber',
        'adm_release' => 'Press Release',
        'adm_sertifikat' => 'Sertifikat',
        'adm_lapgas' => 'Laporan Petugas (Lapgas)'
    ];

    // 3. Hitung Persentase Real-time
    $done = 0;
    foreach($columns as $col => $label) {
        if($event->$col == 'SUDAH') $done++;
    }
    $percentage = round(($done / count($columns)) * 100);

    // 4. Siapkan data untuk dikirim ke View PDF
    $data = [
        'event' => $event,
        'columns' => $columns,
        'percentage' => $percentage,
        'date' => date('d F Y')
    ];

    // 5. Generate PDF
    $pdf = Pdf::loadView('admin.events.pdf_laporan', $data);

    // 6. Download file
    return $pdf->download('Laporan_DTS_'.$event->id.'.pdf');
}

    // ==========================================
    // 🚀 FITUR SMART ARSIP: AI PEMBACA HASIL SCAN OCR
    // ==========================================
    public function parseOcrData(Request $request)
    {
        $rawText = $request->text;
        $apiKey = env('GROQ_API_KEY');

        // Instruksi khusus agar AI mengembalikan JSON murni
        $systemPrompt = "Anda adalah ekstraktor data cerdas. Anda akan menerima teks hasil scan OCR yang berantakan.
        Tugas Anda adalah mengekstrak informasi penting ke dalam format JSON.
        Field yang WAJIB ada di JSON:
        - 'nama_kegiatan' (string)
        - 'lokasi' (string)
        - 'tanggal_mulai' (format YYYY-MM-DD)
        - 'penanggung_jawab' (string)

        Aturan:
        1. Jika data tidak ditemukan, isi dengan ''.
        2. HANYA kembalikan teks format JSON murni. Jangan ada penjelasan apapun.";

        try {
            $response = \Illuminate\Support\Facades\Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.groq.com/openai/v1/chat/completions", [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => "Teks OCR Berantakan: \n" . $rawText]
                ],
                'temperature' => 0.1, // Suhu rendah agar AI tidak berimajinasi
            ]);

            if ($response->successful()) {
                $jsonString = $response->json()['choices'][0]['message']['content'];
                // Bersihkan jika AI memberikan markdown ```json
                $cleanJson = str_replace(['```json', '```'], '', $jsonString);

                return response()->json(json_decode(trim($cleanJson)));
            }

            return response()->json(['error' => 'AI Gagal Merespon'], 500);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ==========================================
    // 🤖 OTOMASI STATUS ADM BERDASARKAN FILE DRIVE
    // ==========================================
  public function syncDriveStatus($id)
{
    // PINDAHKAN API KEY KE DALAM SINI AGAR TIDAK ERROR
    $apiKey = env('GEMINI_API_KEY');

    if (!$apiKey) {
        return response()->json(['status' => 'error', 'message' => 'API Key belum dikonfigurasi di .env!'], 500);
    }

    try {
        $event = \App\Models\Event::findOrFail($id);

        // 1. Ambil Kunci dari JSON
        $keyPath = storage_path('app/google-drive-key.json');
        if (!file_exists($keyPath)) {
            return response()->json(['status' => 'error', 'message' => 'File JSON tidak ada!'], 404);
        }
        $keyData = json_decode(file_get_contents($keyPath), true);

        // 2. Ambil ID Folder
        preg_match('/folders\/([a-zA-Z0-9-_]+)/', $event->lokasi, $matches);
        $folderId = $matches[1] ?? $event->lokasi;

        // 3. GENERATE ACCESS TOKEN
        $now = time();
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'iss' => $keyData['client_email'],
            'scope' => 'https://www.googleapis.com/auth/drive.readonly',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ]));

        $signature = '';
        openssl_sign("$header.$payload", $signature, $keyData['private_key'], 'SHA256');
        $jwt = "$header.$payload." . base64_encode($signature);

        $response = \Illuminate\Support\Facades\Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        $accessToken = $response->json()['access_token'] ?? null;
        if (!$accessToken) {
            return response()->json(['status' => 'error', 'message' => 'Gagal dapet akses token dari Google.'], 500);
        }

        // 4. TEMBAK API GOOGLE DRIVE (Sekarang ambil ID file juga untuk OCR)
        $driveUrl = "https://www.googleapis.com/drive/v3/files";
        $filesResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)->get($driveUrl, [
            'q' => "'$folderId' in parents and trashed = false",
            'fields' => 'files(id, name, mimeType)',
        ]);

        $files = $filesResponse->json()['files'] ?? [];

        // 5. RESET & SYNC DATABASE
        $fields = ['adm_surat', 'adm_dokumentasi', 'adm_daftar_hadir', 'adm_rundown', 'adm_notulensi', 'adm_laporan', 'adm_materi_instruktur', 'adm_materi_narasumber', 'adm_release', 'adm_sertifikat', 'adm_lapgas'];
        foreach($fields as $f) { $event->$f = 'BELUM'; }

        $auditOcr = "";

        foreach ($files as $file) {
            $name = strtoupper($file['name']);
            $fileId = $file['id'];
            $mimeType = $file['mimeType'];

            // --- LOGIKA MAPPING NAMA FILE ---
            if (str_contains($name, 'HADIR')) $event->adm_daftar_hadir = 'SUDAH';
            if (str_contains($name, 'DOKUMENTASI')) $event->adm_dokumentasi = 'SUDAH';
            if (str_contains($name, 'SURAT')) $event->adm_surat = 'SUDAH';
            if (str_contains($name, 'RELEASE')) $event->adm_release = 'SUDAH';
            if (str_contains($name, 'LAPGAS')) $event->adm_lapgas = 'SUDAH';
            if (str_contains($name, 'LAPORAN')) $event->adm_laporan = 'SUDAH';
            if (str_contains($name, 'MATERI')) {
                $event->adm_materi_instruktur = 'SUDAH';
                $event->adm_materi_narasumber = 'SUDAH';
            }
            if (str_contains($name, 'SERTIFIKAT')) $event->adm_sertifikat = 'SUDAH';
            if (str_contains($name, 'NOTULENSI') || str_contains($name, 'ZOOM') || str_contains($name, 'RECORD')) {
                $event->adm_notulensi = 'SUDAH';
            }
            if (str_contains($name, 'RUNDOWN')) $event->adm_rundown = 'SUDAH';

            // --- BAGIAN OCR/VISION: MEMBACA ISI FILE ---
            if (str_contains($name, 'LAPORAN') && (str_contains($mimeType, 'pdf') || str_contains($mimeType, 'image'))) {
                try {
                    $fileData = \Illuminate\Support\Facades\Http::timeout(60)->withToken($accessToken)
                        ->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media");

                    if ($fileData->successful()) {
                        $base64File = base64_encode($fileData->body());

                        // GUNAKAN $apiKey YANG DIAMBIL DARI .env
                        $ocrUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

                        $ocrResponse = \Illuminate\Support\Facades\Http::post($ocrUrl, [
                            'contents' => [[
                                'parts' => [
                                    ['text' => "Identifikasi isi dokumen ini. Apakah benar ini Laporan Kegiatan untuk: {$event->nama_kegiatan}? Sebutkan poin penting yang ada di dalamnya secara singkat (max 3 poin)."],
                                    ['inline_data' => ['mime_type' => $mimeType, 'data' => $base64File]]
                                ]
                            ]]
                        ]);

                        if ($ocrResponse->successful()) {
                            $resJson = $ocrResponse->json();
                            $ocrResult = $resJson['candidates'][0]['content']['parts'][0]['text'] ?? 'AI tidak bisa membaca detail.';
                            $auditOcr = "\n\n🔍 **Analisis Isi PDF:**\n" . $ocrResult;
                        }
                    }
                } catch (\Exception $e) { }
            }
        }

        // --- BAGIAN AI GEMINI (SARAN) ---
        $statusDoc = "";
        foreach($fields as $f) {
            $statusDoc .= "- " . str_replace('adm_', '', $f) . ": " . $event->$f . "\n";
        }

        $prompt = "Tolong analisis kelengkapan dokumen untuk kegiatan: {$event->nama_kegiatan}.
                   Status saat ini:\n{$statusDoc}\n
                   Jika sudah 100%, beri pujian singkat. Jika belum, sebutkan 1 dokumen yang paling penting untuk diunggah sekarang.
                   Gunakan gaya bahasa asisten yang ramah.";

        $aiAnalysis = "Ayo semangat lengkapi administrasinya!";

        try {
            // GUNAKAN $apiKey YANG DIAMBIL DARI .env
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

            $aiResponse = \Illuminate\Support\Facades\Http::withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);

            if ($aiResponse->successful()) {
                $aiData = $aiResponse->json();
                $aiAnalysis = ($aiData['candidates'][0]['content']['parts'][0]['text'] ?? "Analisis selesai.") . $auditOcr;
            }
        } catch (\Exception $e) {
            $aiAnalysis = "Koneksi ke server AI gagal: " . $e->getMessage();
        }

        $event->ai_analysis = $aiAnalysis;
        $event->save();

        $doneCount = 0;
        foreach($fields as $field) {
            if($event->$field == 'SUDAH') $doneCount++;
        }
        $realPercentage = round(($doneCount / count($fields)) * 100);

        return response()->json([
            'status' => 'success',
            'percentage' => $realPercentage,
            'message' => $aiAnalysis,
            'analysis' => $aiAnalysis
        ]);

    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Gagal: ' . $e->getMessage()], 500);
    }
}

/**
     * Fungsi Pembantu untuk mengambil daftar nama file dari Google Drive
     */
    private function getFilesFromDrive($link)
{
    try {
        // 1. Ambil Kunci JSON (Pastikan file ini ada di storage/app/)
        $keyPath = storage_path('app/google-drive-key.json');
        if (!file_exists($keyPath)) return "Gagal: File google-drive-key.json tidak ditemukan!";
        $keyData = json_decode(file_get_contents($keyPath), true);

        // 2. Ekstrak ID Folder
        preg_match('/[-\w]{25,}/', $link, $matches);
        $folderId = $matches[0] ?? null;
        if (!$folderId) return "Gagal: ID Folder Drive tidak valid.";

        // 3. GENERATE ACCESS TOKEN (JWT OAUTH2)
        $now = time();
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'iss' => $keyData['client_email'],
            'scope' => 'https://www.googleapis.com/auth/drive.readonly',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600, 'iat' => $now
        ]));
        $signature = '';
        openssl_sign("$header.$payload", $signature, $keyData['private_key'], 'SHA256');
        $jwt = "$header.$payload." . base64_encode($signature);

        $tokenResponse = \Illuminate\Support\Facades\Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        $accessToken = $tokenResponse->json()['access_token'] ?? null;
        if (!$accessToken) return "Gagal dapet akses token dari Google.";

        // 4. AMBIL DAFTAR FILE
        $driveUrl = "https://www.googleapis.com/drive/v3/files";
        $filesResponse = \Illuminate\Support\Facades\Http::withToken($accessToken)->get($driveUrl, [
            'q' => "'$folderId' in parents and trashed = false",
            'fields' => 'files(name)',
        ]);

        $files = $filesResponse->json()['files'] ?? [];
        if (empty($files)) return "Folder kosong.";

        $names = [];
        foreach ($files as $f) { $names[] = "- " . $f['name']; }
        return implode("\n", $names);

    } catch (\Exception $e) {
        return "Gagal akses Drive: " . $e->getMessage();
    }
}

    public function syncDrive($id)
{
    try {
        $assignment = \App\Models\Assignment::findOrFail($id);
        $linkDrive = $assignment->link_bukti;

        if (!$linkDrive || $linkDrive == '-') {
            return response()->json(['status' => 'error', 'message' => 'Link Drive belum diisi, Min!']);
        }

        // 1. Ambil daftar file (Gunakan fungsi yang sama dengan DTS)
        $fileListText = $this->getFilesFromDrive($linkDrive);

        $apiKey = env('GEMINI_API_KEY');
        // Pastikan endpoint-nya v1beta ya Min
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $apiKey;

        // 2. Kirim Request (Pakai pengaman SSL . tanpa pengecekan ketat)
        $response = \Illuminate\Support\Facades\Http::withoutVerifying()
            ->timeout(30)
            ->post($url, [
                "contents" => [
                    ["parts" => [["text" => "Daftar file: $fileListText . Cek kelengkapan Lapgas (Surat Tugas, Daftar Hadir, Dokumentasi, Laporan). Jawab hanya JSON: {\"status_adm\":\"LENGKAP/BELUM\", \"catatan_ai\":\"...\"}"]] ]
                ]
            ]);

        // --- 🕵️ BAGIAN DIAGNOSA (Cek kenapa dia gagal) ---
        if ($response->failed()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Google Nolak, Min! Respon: ' . $response->body()
            ]);
        }
        // -----------------------------------------------

        $result = $response->json();
        $aiText = $result['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

        // Saring JSON-nya
        if (preg_match('/\{.*\}/s', $aiText, $matches)) {
            $cleanJson = json_decode($matches[0], true);
        } else {
            $cleanJson = null;
        }

        if ($cleanJson && isset($cleanJson['status_adm'])) {
            $assignment->update([
                'status_adm' => $cleanJson['status_adm'],
                'catatan_ai' => $cleanJson['catatan_ai']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Analisis Berhasil!',
                'result' => $cleanJson
            ]);
        }

        return response()->json(['status' => 'error', 'message' => 'AI jawab tapi formatnya bukan JSON.']);

    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Sistem Error: ' . $e->getMessage()]);
    }
}
}

