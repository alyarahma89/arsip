<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Kegiatan</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/komdigi.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8fafc; font-family: 'Inter', sans-serif; }
        .card-custom { border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); background: white; }
        .form-label { font-weight: 600; font-size: 0.9rem; color: #334155; }
    </style>
</head>
<body class="p-4">
    <div class="container" style="max-width: 1100px;">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1 text-dark">Edit Kegiatan</h3>
                <p class="text-muted mb-0">Perbarui data kegiatan yang sudah ada.</p>
            </div>
            <a href="{{ route('admin.events') }}" class="btn btn-light border shadow-sm"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
        </div>

        <form action="{{ route('events.update', $event->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card-custom p-4 h-100">
                        <h5 class="fw-bold mb-4 text-warning border-bottom pb-2"><i class="fas fa-edit me-2"></i>Informasi Kegiatan</h5>

                        {{-- 1. NAMA KEGIATAN --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control" required value="{{ $event->nama_kegiatan }}">
                        </div>

                        {{-- 2. AKADEMI (DROPDOWN) - WAJIB ADA --}}
                        <div class="mb-3">
                            <label class="form-label">Pilih Akademi <span class="text-danger">*</span></label>
                            <select name="akademi" class="form-select" required>
                                <option value="" disabled>-- Pilih Akademi --</option>
                                <option value="DEA" {{ $event->akademi == 'DEA' ? 'selected' : '' }}>DEA</option>
                                <option value="TA" {{ $event->akademi == 'TA' ? 'selected' : '' }}>TA</option>
                                <option value="VSGA" {{ $event->akademi == 'VSGA' ? 'selected' : '' }}>VSGA</option>
                                <option value="GTA" {{ $event->akademi == 'GTA' ? 'selected' : '' }}>GTA</option>
                                <option value="FGA" {{ $event->akademi == 'FGA' ? 'selected' : '' }}>FGA</option>
                                <option value="TIK" {{ $event->akademi == 'TIK' ? 'selected' : '' }}>TIK</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" class="form-control" required value="{{ $event->tanggal_mulai ? \Carbon\Carbon::parse($event->tanggal_mulai)->format('Y-m-d') : '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control" value="{{ $event->tanggal_selesai ? \Carbon\Carbon::parse($event->tanggal_selesai)->format('Y-m-d') : '' }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Penanggung Jawab (PJ)</label>
                            <input type="text" name="penanggung_jawab" class="form-control" value="{{ $event->penanggung_jawab }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Panitia / Anggota</label>
                            <textarea name="panitia" class="form-control" rows="2">{{ $event->panitia }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-success"><i class="fab fa-google-drive me-1"></i> Link Folder DTS</label>
                            <input type="text" name="lokasi" class="form-control" value="{{ $event->lokasi }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-warning"><i class="fas fa-file-alt me-1"></i> Link Pencatatan Arsip</label>
                            <input type="text" name="link_pencatatan" class="form-control" value="{{ $event->link_pencatatan }}">
                        </div>
                    </div>
                </div>

                {{-- KOLOM KANAN: STATUS ADMIN (DROPDOWN JUGA) --}}
                <div class="col-lg-5">
                    <div class="card-custom p-4 h-100 bg-light border-0">
                        <h5 class="fw-bold mb-4 text-success border-bottom pb-2"><i class="fas fa-check-double me-2"></i>Status Kelengkapan Admin</h5>

                        <div class="d-flex flex-column gap-3">
                            @php
                                $checks = [
                                    'adm_surat' => 'Surat Undangan / Tugas',
                                    'adm_dokumentasi' => 'Dokumentasi Foto',
                                    'adm_daftar_hadir' => 'Daftar Hadir',
                                    'adm_rundown' => 'Rundown Acara',
                                    'adm_notulensi' => 'Notulensi',
                                    'adm_laporan' => 'Laporan Akhir',
                                    'adm_materi_instruktur' => 'Materi Instruktur',
                                    'adm_materi_narasumber' => 'Materi Narasumber',
                                    'adm_release' => 'Press Release / Berita',
                                    'adm_sertifikat' => 'Sertifikat',
                                    'adm_lapgas' => 'Laporan Tugas (Lapgas)'
                                ];
                            @endphp

                            @foreach($checks as $key => $label)
                            <div class="bg-white p-3 rounded shadow-sm border">
                                <label class="form-label mb-2 d-block small text-muted text-uppercase fw-bold">{{ $label }}</label>
                                <select name="{{ $key }}" class="form-select form-select-sm fw-medium">
                                    <option value="BELUM" {{ $event->$key == 'BELUM' ? 'selected' : '' }} class="text-danger fw-bold">BELUM</option>
                                    <option value="BELUM LENGKAP" {{ $event->$key == 'BELUM LENGKAP' ? 'selected' : '' }} class="text-warning fw-bold">BELUM LENGKAP</option>
                                    <option value="SUDAH" {{ $event->$key == 'SUDAH' ? 'selected' : '' }} class="text-success fw-bold">SUDAH</option>
                                    <option value="TIDAK ADA" {{ $event->$key == 'TIDAK ADA' ? 'selected' : '' }} class="text-muted">TIDAK ADA</option>
                                </select>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 text-end mb-5">
                <button type="submit" class="btn btn-warning btn-lg px-5 shadow rounded-pill fw-bold text-white">
                    <i class="fas fa-save me-2"></i> Update Data
                </button>
            </div>
        </form>
    </div>
</body>
</html>
