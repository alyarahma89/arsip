<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArchiveController;

/*
|--------------------------------------------------------------------------
| Web Routes - SISTEM ARSIP BBPSDMP KOMDIGI
|--------------------------------------------------------------------------
*/

// 1. HALAMAN UTAMA & AUTH
Route::get('/', function () {
    return redirect()->route('login');
});

// Route Reload Captcha
Route::get('/reload-captcha', [AuthController::class, 'reloadCaptcha'])->name('captcha.reload');

// --- GUEST ONLY (Belum Login) ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginProcess'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'processRegister'])->name('register.submit');
});

// --- AUTH REQUIRED (Sudah Login) ---
Route::middleware(['auth'])->group(function () {

    // DASHBOARD & PROFIL
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [DashboardController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // === FITUR PEGAWAI ===
    Route::get('/pegawai/dts', [DashboardController::class, 'pegawaiDts'])->name('pegawai.dts');
    Route::get('/pegawai/lapgas', [DashboardController::class, 'pegawaiLapgas'])->name('pegawai.lapgas');
    Route::put('/pegawai/lapgas/{id}', [DashboardController::class, 'uploadLapgas'])->name('pegawai.uploadLapgas');
    Route::get('/pegawai/export-rekap', [DashboardController::class, 'exportRekap'])->name('pegawai.export_rekap');

    // === FITUR ADMIN (DATA & ANALISIS) ===
    Route::get('/admin/events', [DashboardController::class, 'listEvents'])->name('admin.events');
    Route::get('/admin/assignments', [DashboardController::class, 'assignments'])->name('admin.assignments');
    Route::get('/admin/analisis', [DashboardController::class, 'analisis'])->name('admin.analisis');
    Route::get('/history', [DashboardController::class, 'history'])->name('admin.history');

    // IMPORT DATA
    Route::post('/import/dts', [DashboardController::class, 'importDTS'])->name('import.dts');
    Route::post('/import/lapgas', [DashboardController::class, 'importLapgas'])->name('import.lapgas');

    // CRUD KEGIATAN DTS
    Route::get('/events/create', [DashboardController::class, 'createEvent'])->name('events.create');
    Route::post('/events/store', [DashboardController::class, 'storeEvent'])->name('events.store');
    Route::get('/events/{id}/edit', [DashboardController::class, 'editEvent'])->name('events.edit');
    Route::put('/events/{id}/update', [DashboardController::class, 'updateEvent'])->name('events.update');
    Route::delete('/events/{id}/delete', [DashboardController::class, 'deleteEvent'])->name('events.delete');
    Route::delete('/admin/events/truncate', [DashboardController::class, 'truncateEvents'])->name('events.truncate');

    // CRUD LAPGAS (MANUAL & UPDATE)
    Route::get('/assignments/create', [DashboardController::class, 'createAssignment'])->name('admin.assignments.create');
    Route::post('/assignments/store', [DashboardController::class, 'storeAssignment'])->name('admin.assignments.store');
    Route::put('/assignments/{id}', [DashboardController::class, 'updateAssignment'])->name('admin.assignments.update');
    Route::delete('/assignments/{id}', [DashboardController::class, 'deleteAssignment'])->name('admin.assignments.delete');

    // === FITUR EKSPOR (PDF & EXCEL) ===
    // Lapgas
    Route::get('/assignments/export/excel', [DashboardController::class, 'exportExcelLapgas'])->name('admin.assignments.export.excel');
    Route::get('/assignments/export/csv', [DashboardController::class, 'exportCsvLapgas'])->name('admin.assignments.export.csv');
    Route::get('/assignments/export/pdf', [DashboardController::class, 'exportPdfLapgas'])->name('admin.assignments.export.pdf');
    // DTS
    Route::get('/events/export/excel', [DashboardController::class, 'exportExcelEvent'])->name('admin.events.export.excel');
    Route::get('/events/export/csv', [DashboardController::class, 'exportCsvEvent'])->name('admin.events.export.csv');
    Route::get('/events/export/pdf', [DashboardController::class, 'exportPdfEvent'])->name('admin.events.export.pdf');
    // Cetak Laporan Satuan
    Route::get('/admin/dts/cetak/{id}', [DashboardController::class, 'cetakLaporanPdf'])->name('dts.cetak');

    // === FITUR AI & SMART SCAN ===
    Route::post('/ai/enhance-text', [DashboardController::class, 'enhanceText'])->name('ai.enhance');
    Route::post('/api/parse-ocr', [DashboardController::class, 'parseOcrData'])->name('api.parse.ocr');

    // Rute Sinkronisasi Drive Real-time (Pemicu Status Hijau)
    Route::get('/admin/events/sync-drive/{id}', [DashboardController::class, 'syncDriveStatus'])->name('admin.sync.drive');

    // Chatbot ARAI
    Route::post('/chat-arai', [DashboardController::class, 'chatArai'])->name('chat.arai');

    // Pintu untuk melihat halaman (GET)
    Route::get('/admin/archives', [ArchiveController::class, 'index'])->name('admin.archives.index');

    // Pintu untuk proses upload file (POST) - INI YANG KURANG
    Route::post('/admin/archives/import', [ArchiveController::class, 'import'])->name('admin.archives.import');

    // Route untuk menghapus seluruh data di tabel archives
    Route::delete('/admin/archives/truncate', [ArchiveController::class, 'truncate'])->name('admin.archives.truncate');

    // Route untuk Export (Excel & PDF)
    Route::get('/admin/archives/export/{type}', [ArchiveController::class, 'export'])->name('admin.archives.export');

    // Route untuk memproses simpan data manual & upload foto berkas
    Route::post('/admin/archives/store', [ArchiveController::class, 'store'])->name('admin.archives.store');

    // Route untuk memproses analisis gambar/PDF pakai AI Groq
    Route::post('/admin/archives/analyze-ai', [ArchiveController::class, 'analyzeAI'])->name('admin.archives.analyze-ai');

    // Route untuk AI Analisis Lapgas
    // Pastikan tujuannya DashboardController ya Min!
    Route::post('/admin/assignments/sync/{id}', [\App\Http\Controllers\DashboardController::class, 'syncDrive'])->name('admin.assignments.sync');




});
