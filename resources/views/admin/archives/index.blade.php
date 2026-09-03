<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Induk Arsip - BBPSDM KOMDIGI</title>
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --sidebar-width: 260px;
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; color: #334155; }

        /* Sidebar Styling */
        .sidebar {
            width: var(--sidebar-width); background: white; height: 100vh; position: fixed;
            left: 0; top: 0; box-shadow: 0 0 20px rgba(0, 0, 0, 0.08); z-index: 100;
            padding-top: 20px; transition: all 0.3s ease; display: flex; flex-direction: column;
        }
        .sidebar-logo { padding: 0 1.5rem; }
        .sidebar-menu { padding: 0 1rem; flex-grow: 1; }
        .sidebar-menu .nav-link { color: #64748b; border-radius: 10px; margin-bottom: 8px; padding: 12px 15px; transition: all 0.2s; display: flex; align-items: center; text-decoration: none; }
        .sidebar-menu .nav-link:hover, .sidebar-menu .nav-link.active { background: var(--primary-gradient); color: white; transform: translateX(5px); }
        .sidebar-menu .nav-link i { width: 24px; text-align: center; margin-right: 10px; }

        /* Main Content */
        .main-content { margin-left: var(--sidebar-width); padding: 20px; min-height: 100vh; transition: all 0.3s; }
        .dashboard-header { background: white; border-radius: 15px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border-left: 5px solid #4361ee; }

        /* Table Styling Modern */
        .table-container { background: white; border-radius: 15px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); overflow: hidden; border: 1px solid #e2e8f0; }
        .table-responsive { max-height: 75vh; overflow-y: auto; overflow-x: auto; }
        .table thead th { background: #f8fafc; vertical-align: middle; position: sticky; top: 0; z-index: 10; border-bottom: 2px solid #e2e8f0; }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }

        .shadow-hover:hover div, .img-hover:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-logo d-flex align-items-center mb-4 pb-3 border-bottom mx-3 mt-2">
            <img src="{{ asset('image/komdigi.png') }}" style="width: 45px; margin-right: 12px;">
            <div>
                <h6 class="fw-bold mb-0 text-dark">Sistem Arsip</h6>
                <small class="text-muted" style="font-size: 0.7rem;">BBPSDM KOMDIGI</small>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="nav flex-column p-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i> Data DTS</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.assignments') }}"><i class="fas fa-tasks"></i> Data Lapgas</a></li>
                <li class="nav-item"><a class="nav-link active" href="#"><i class="fas fa-file-invoice"></i> Buku Induk Arsip</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.analisis') }}"><i class="fas fa-chart-bar"></i> Analisis Laporan</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.history') }}"><i class="fas fa-history"></i> Riwayat Upload</a></li>
            </ul>
        </div>

        <div class="mt-auto p-3 border-top mx-3 mb-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger w-100 rounded-pill fw-bold" type="submit"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
            </form>
        </div>
    </div>

    <div class="main-content">
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h3 class="fw-bold mb-1">Buku Induk Arsip 2025</h3>
                    <p class="text-muted mb-0 small">Manajemen database arsip aktif & inaktif</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#addManualModal">
                        <i class="fas fa-camera-retro me-2"></i> Tambah Arsip (Kamera/AI)
                    </button>
                    <form action="{{ route('admin.archives.truncate') }}" method="POST" onsubmit="return confirm('Hapus seluruh data arsip? Tindakan ini permanen!');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger rounded-pill px-3"><i class="fas fa-trash-alt me-1"></i> Kosongkan</button>
                    </form>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary rounded-pill px-3 dropdown-toggle" data-bs-toggle="dropdown"><i class="fas fa-download me-1"></i> Export</button>
                        <ul class="dropdown-menu shadow border-0">
                            <li><a class="dropdown-item" href="{{ route('admin.archives.export', ['type' => 'excel']) }}"><i class="fas fa-file-excel text-success me-2"></i> Excel</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.archives.export', ['type' => 'pdf']) }}"><i class="fas fa-file-pdf text-danger me-2"></i> PDF</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fas fa-file-excel me-1"></i> Import</button>
                </div>
            </div>
        </div>

        {{-- SEARCH BAR & FILTER CERDAS --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form action="{{ route('admin.archives.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-0 bg-light" placeholder="Cari Surat, Kode, atau Uraian..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <select name="tahun" class="form-select border-0 bg-light" onchange="this.form.submit()">
                            <option value="">Semua Tahun</option>
                            @php $currentYear = date('Y'); @endphp
                            @for($i = $currentYear + 1; $i >= 2020; $i--)
                                <option value="{{ $i }}" {{ request('tahun') == $i ? 'selected' : '' }}>Tahun {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="status" class="form-select border-0 bg-light" onchange="this.form.submit()">
                            <option value="">Semua Keterangan JRA</option>
                            <option value="Permanen" {{ request('status') == 'Permanen' ? 'selected' : '' }}>🔵 Permanen</option>
                            <option value="Musnah" {{ request('status') == 'Musnah' ? 'selected' : '' }}>🔴 Musnah</option>
                            <option value="Dinilai Kembali" {{ request('status') == 'Dinilai Kembali' ? 'selected' : '' }}>🟡 Dinilai Kembali</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100 rounded-3"><i class="fas fa-filter"></i></button>
                    </div>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TABEL MODERN (Anti Scroll Panjang) --}}
        <div class="table-container shadow-sm border-0 rounded-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 bg-white">
                    <thead class="bg-light text-muted" style="font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase;">
                        <tr>
                            <th class="ps-4 py-3">Berkas</th>
                            <th class="py-3">Identitas Arsip</th>
                            <th class="py-3" style="width: 35%;">Uraian & Isi Informasi</th>
                            <th class="py-3">Fisik & Lokasi</th>
                            <th class="py-3">Jadwal Retensi (JRA)</th>
                            <th class="py-3 text-center pe-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($archives as $archive)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($archive->foto_berkas)
                                            @php $ext = pathinfo($archive->foto_berkas, PATHINFO_EXTENSION); @endphp
                                            @if(in_array($ext, ['jpg','jpeg','png']))
                                                <a href="{{ asset('uploads/archives/'.$archive->foto_berkas) }}" target="_blank">
                                                    <img src="{{ asset('uploads/archives/'.$archive->foto_berkas) }}" width="45" height="45" class="rounded-3 border shadow-sm" style="object-fit: cover;">
                                                </a>
                                            @elseif($ext == 'pdf')
                                                <a href="{{ asset('uploads/archives/'.$archive->foto_berkas) }}" target="_blank" class="text-danger"><i class="fas fa-file-pdf fa-2x"></i></a>
                                            @else
                                                <a href="{{ asset('uploads/archives/'.$archive->foto_berkas) }}" target="_blank" class="text-success"><i class="fas fa-file-excel fa-2x"></i></a>
                                            @endif
                                        @else
                                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted border" style="width:45px; height:45px;">
                                                <i class="fas fa-folder"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">No. Urut: <strong>{{ $archive->no_urut }}</strong></small>
                                        <small class="text-muted d-block">Tgl: {{ $archive->tanggal_surat ? date('d/m/Y', strtotime($archive->tanggal_surat)) : '-' }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary mb-1 border border-primary-subtle px-2 py-1">{{ $archive->kode_klasifikasi }}</span>
                                <div class="fw-bold small text-dark mb-1">{{ $archive->no_berkas }}</div>
                                <span class="badge bg-light text-dark border"><i class="fas fa-clock me-1"></i>Thn: {{ $archive->kurun_waktu }}</span>
                            </td>

                            <td>
                                <div class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">{{ $archive->uraian_berkas }}</div>
                                <div class="text-muted small text-truncate" style="max-width: 400px;" title="{{ $archive->uraian_isi }}">
                                    <i class="fas fa-align-left me-1"></i> {{ $archive->uraian_isi }}
                                </div>
                                <div class="mt-1">
                                    <span class="badge bg-info bg-opacity-10 text-info fw-normal border border-info-subtle" style="font-size: 0.7rem;">Isi: {{ $archive->no_isi_berkas }}</span>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal border border-secondary-subtle ms-1" style="font-size: 0.7rem;">{{ $archive->tingkat_perkembangan }}</span>
                                </div>
                            </td>

                            <td>
                                <div class="small mb-1"><i class="fas fa-copy text-muted me-1"></i> {{ $archive->jumlah_lembar }} Lembar</div>
                                <div class="small mb-1"><i class="fas fa-map-marker-alt text-danger me-1"></i> Lks: {{ $archive->lokasi_fisik }}</div>
                                <div class="small"><i class="fas fa-box text-warning me-1"></i> Fld: {{ $archive->no_folder }}</div>
                            </td>

                            <td>
                                <div class="small mb-1">Aktif: <strong class="text-success">{{ $archive->masa_aktif }} Thn</strong></div>
                                <div class="small mb-1">Inaktif: <strong class="text-warning">{{ $archive->masa_inaktif }} Thn</strong></div>
                                <span class="badge bg-dark bg-opacity-10 text-dark border mt-1" style="font-size: 0.65rem;">Keamanan: {{ $archive->klasifikasi_keamanan }}</span>
                            </td>

                            <td class="text-center pe-4">
                                @if(strtolower(trim($archive->status_akhir)) == 'permanen')
                                    <span class="badge bg-success px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-shield-alt me-1"></i> PERMANEN</span>
                                @elseif(strtolower(trim($archive->status_akhir)) == 'musnah')
                                    <span class="badge bg-danger px-3 py-2 rounded-pill shadow-sm"><i class="fas fa-fire me-1"></i> MUSNAH</span>
                                @else
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill shadow-sm">{{ strtoupper($archive->status_akhir) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="py-5 text-muted text-center"><i class="fas fa-inbox fa-3x mb-3 text-light"></i><br>Data arsip tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH MANUAL + AI ANALISIS --}}
    <div class="modal fade" id="addManualModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content border-0 shadow-lg" action="{{ route('admin.archives.store') }}" method="POST" enctype="multipart/form-data" style="border-radius: 20px;">
                @csrf
                <div class="modal-header bg-primary text-white border-0 pt-4 px-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-file-medical me-2"></i> Input Berkas Manual</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label small fw-bold">Ambil Foto / Upload Berkas (Gambar/PDF)</label>
                        <div class="input-group">
                            <input type="file" id="ai_file_input" name="foto_berkas"
                                class="form-control rounded-start-pill border-2"
                                accept="image/*, .pdf, application/pdf, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument"
                                capture="environment">

                            <button type="button" id="btn_analisis_ai" class="btn btn-dark rounded-end-pill px-3">
                                <i class="fas fa-robot me-1"></i> Analisis AI
                            </button>
                        </div>
                        <div id="ai_loading" class="mt-2 small text-primary fw-bold" style="display:none;">
                            <i class="fas fa-spinner fa-spin me-1"></i> AI sedang membaca berkas...
                        </div>
                        <small class="text-muted">Mendukung Kamera HP, PDF. (Excel harap input manual)</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Kode Klasifikasi</label>
                        <input type="text" id="ai_kode" name="kode_klasifikasi" class="form-control rounded-pill" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">Tanggal Surat</label>
                        <input type="date" id="ai_tanggal" name="tanggal_surat" class="form-control rounded-pill">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label small fw-bold">Uraian Berkas (Nama Kegiatan)</label>
                        <input type="text" id="ai_uraian_berkas" name="uraian_berkas" class="form-control rounded-pill" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label small fw-bold">Uraian Isi (Keterangan Dokumen)</label>
                        <textarea id="ai_uraian_isi" name="uraian_isi" class="form-control" rows="2" style="border-radius: 15px;" required></textarea>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">Kurun Waktu (Tahun)</label>
                        <input type="number" name="kurun_waktu" class="form-control rounded-pill" value="{{ date('Y') }}">
                    </div>
                </div>
                </div>
                <div class="modal-footer border-0 pb-4 px-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Berkas</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL IMPORT --}}
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content border-0 shadow" action="{{ route('admin.archives.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-success">Import Buku Induk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="file_excel" class="form-control rounded-pill" accept=".xlsx, .xls" required>
                    <p class="small text-muted mt-2 mb-0 ms-2">Format: Spreadsheet DTS 2025</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold">Proses & Sinkronisasi</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('btn_analisis_ai').addEventListener('click', function() {
        const fileInput = document.getElementById('ai_file_input');
        const loading = document.getElementById('ai_loading');
        const btn = this;

        if (fileInput.files.length === 0) {
            alert("Pilih file atau jepret kamera dulu Min!");
            return;
        }

        // PENGAMAN EXCEL FRONTEND
        const file = fileInput.files[0];
        if (file.name.match(/\.(xls|xlsx)$/i) || file.type.includes('spreadsheet') || file.type.includes('excel')) {
            alert("Waduh Min, tombol AI cuma bisa baca Foto (Gambar) atau PDF! \n\nKalau berkasnya Excel, langsung isi datanya manual dan klik 'Simpan Berkas' aja ya.");
            return;
        }

        loading.style.display = 'block';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        fetch("{{ route('admin.archives.analyze-ai') }}", {
            method: 'POST',
            body: formData
        })
        .then(async response => {
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                return response.json();
            } else {
                const textError = await response.text();
                console.error("Error dari Server PHP:", textError);
                throw new Error("Server PHP ngasih error (Bukan JSON). Cek tab Console/Network!");
            }
        })
        .then(data => {
            console.log("Respon AI:", data);
            if (data.status === 'success') {
                const res = data.result;
                document.getElementById('ai_kode').value = res.kode_klasifikasi || '';
                document.getElementById('ai_uraian_berkas').value = res.nama_kegiatan || '';
                document.getElementById('ai_uraian_isi').value = res.perihal_surat || '';
                document.getElementById('ai_tanggal').value = res.tanggal || '';
                alert("Berhasil! Data terisi otomatis.");
            } else {
                alert("Gagal: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error Fetch:", error);
            alert("Koneksi bermasalah atau Server Error. Cek inspect element > console!");
        })
        .finally(() => {
            loading.style.display = 'none';
            btn.disabled = false;
        });
    });
    </script>
</body>
</html>
