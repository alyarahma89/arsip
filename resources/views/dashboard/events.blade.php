<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar Kegiatan DTS - Komdigi</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/komdigi.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --komdigi-blue-dark: #0E2C6C; --komdigi-blue-light: #00AEEF; --komdigi-accent: #FDB913; --komdigi-bg: #F4F7F9;
            --border: #e2e8f0; --white: #ffffff; --text-dark: #1e293b; --text-muted: #64748b;
        }
        body { background-color: var(--komdigi-bg); font-family: 'Inter', sans-serif; color: var(--text-dark); }
        .header-section {
            background: linear-gradient(135deg, var(--komdigi-blue-dark) 0%, #1a4299 100%);
            color: white; border-radius: 12px; padding: 2rem; margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(14, 44, 108, 0.2); position: relative; overflow: hidden;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px; padding: 1.5rem; height: 100%; color: white; transition: all 0.3s ease; text-decoration: none; display: block;
        }
        .stat-card:hover { background: rgba(255, 255, 255, 0.2); transform: translateY(-5px); }
        .stat-card.active { border: 1px solid var(--komdigi-accent); background: rgba(255, 255, 255, 0.25); }

        .card-custom { background: var(--white); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); margin-bottom: 24px; }
        .table-container { overflow-x: auto; border-radius: 12px; background: var(--white); border: 1px solid var(--border); }
        .table-custom thead th { background-color: #f8fafc; color: var(--komdigi-blue-dark); font-weight: 700; font-size: 0.75rem; text-transform: uppercase; padding: 12px; }
        .table-custom td { padding: 12px; vertical-align: middle; font-size: 0.85rem; border-bottom: 1px solid var(--border); }

        /* Style Khusus Sidebar AI Melayang */
        .offcanvas-ai { width: 400px !important; border-left: 5px solid var(--komdigi-blue-light) !important; }
        .ai-content-box { font-size: 0.9rem; line-height: 1.7; color: #334155; }
        .ai-content-box b { color: var(--komdigi-blue-dark); }
    </style>
</head>
<body class="p-4">

    @php
        $fields = ['adm_surat', 'adm_dokumentasi', 'adm_daftar_hadir', 'adm_rundown', 'adm_notulensi', 'adm_laporan', 'adm_materi_instruktur', 'adm_materi_narasumber', 'adm_release', 'adm_sertifikat', 'adm_lapgas'];
    @endphp

    <div class="container-fluid">
        {{-- HEADER SECTION --}}
        <div class="header-section shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="fas fa-layer-group me-2"></i>Daftar Kegiatan DTS</h2>
                    <p class="mb-0 opacity-75">Manajemen Administrasi & Arsip Digital BBPSDMP</p>
                </div>
                <div>
                    <form action="{{ route('events.truncate') }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus semua data?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm shadow-sm me-2"><i class="fas fa-trash me-1"></i>Hapus Semua</button>
                    </form>
                    <a href="{{ route('events.create') }}" class="btn btn-light btn-sm shadow-sm me-2"><i class="fas fa-plus me-1"></i>Tambah</a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm shadow-sm"><i class="fas fa-home me-1"></i>Home</a>
                </div>
            </div>

            <div class="row g-3">
                @foreach([
                    ['all', 'database', $totalEvents, 'Total Kegiatan', ''],
                    ['complete', 'check-double', $countLengkap, 'Lengkap', 'text-success'],
                    ['has-archive', 'box-archive', $countArsip, 'Arsip Drive', 'text-warning'],
                    ['incomplete', 'clock', $countPerluTindakan, 'Perlu Tindakan', 'text-danger']
                ] as $stat)
                <div class="col-md-3">
                    <a href="{{ route('admin.events', ['filter' => $stat[0]]) }}" class="stat-card {{ request('filter') == $stat[0] || (!request('filter') && $stat[0] == 'all') ? 'active' : '' }}">
                        <div class="d-flex align-items-center">
                            <div class="me-3 fs-3 opacity-50"><i class="fas fa-{{ $stat[1] }}"></i></div>
                            <div><h4 class="fw-bold mb-0">{{ $stat[2] }}</h4><div class="small opacity-75">{{ $stat[3] }}</div></div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>

        <div class="row">
            {{-- TABEL SEKARANG FULL (col-12) --}}
            <div class="col-12">
                <div class="card-custom p-3 mb-4">
                    <form action="{{ route('admin.events') }}" method="GET" id="filterForm">
                        @if(request('filter')) <input type="hidden" name="filter" value="{{ request('filter') }}"> @endif
                        <input type="hidden" name="month" id="inputMonth" value="{{ request('month') }}">
                        <input type="hidden" name="akademi" id="inputAkademi" value="{{ request('akademi') }}">
                        <input type="hidden" name="kategori_arsip" id="inputKategori" value="{{ request('kategori_arsip') }}">

                        <div class="row g-2 align-items-center">
                            <div class="col-auto d-flex gap-2">
                                <button class="btn btn-white border shadow-sm" type="button" data-bs-toggle="dropdown" title="Bulan"><i class="far fa-calendar-alt text-primary"></i></button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="applyFilter('inputMonth', '')">Semua Bulan</a></li>
                                    @foreach(range(1,12) as $m)
                                        <li><a class="dropdown-item" href="#" onclick="applyFilter('inputMonth', '{{ $m }}')">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</a></li>
                                    @endforeach
                                </ul>

                                <button class="btn btn-white border shadow-sm" type="button" data-bs-toggle="dropdown" title="Akademi"><i class="fas fa-graduation-cap text-primary"></i></button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="applyFilter('inputAkademi', '')">Semua</a></li>
                                    @foreach(['VSGA', 'DEA', 'TA', 'GTA', 'FGA'] as $akd)
                                        <li><a class="dropdown-item" href="#" onclick="applyFilter('inputAkademi', '{{ $akd }}')">{{ $akd }}</a></li>
                                    @endforeach
                                </ul>

                                <button class="btn btn-white border shadow-sm" type="button" data-bs-toggle="dropdown" title="Arsip"><i class="fas fa-folder-tree text-primary"></i></button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" onclick="applyFilter('inputKategori', '')">Semua Arsip</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="applyFilter('inputKategori', 'aktif')">Aktif</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="applyFilter('inputKategori', 'inaktif')">Inaktif</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="applyFilter('inputKategori', 'vital')">Vital</a></li>
                                </ul>
                            </div>

                            <div class="col">
                                <div class="input-group shadow-sm">
                                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari kegiatan..." value="{{ request('search') }}">
                                    <button class="btn btn-primary px-4" type="submit">Cari</button>
                                </div>
                            </div>

                            <div class="col-auto">
                                <div class="dropdown">
                                    <button class="btn btn-success shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-download me-1"></i> Eksport
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                        <li><a class="dropdown-item" href="{{ route('admin.events.export.excel', request()->all()) }}"><i class="fas fa-file-excel me-2 text-success"></i>Excel</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.events.export.pdf', request()->all()) }}"><i class="fas fa-file-pdf me-2 text-danger"></i>PDF</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.events.export.csv', request()->all()) }}"><i class="fas fa-file-csv me-2 text-info"></i>CSV</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="table-container shadow-sm">
                    <table class="table table-custom mb-0">
                        <thead class="text-center">
                            <tr>
                                <th>No</th><th>Akademi</th><th class="text-start">Nama Kegiatan</th><th>Tanggal</th><th>PJ</th><th>Adm</th><th>Arsip</th><th>DTS</th><th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($events as $index => $event)
                            <tr class="text-center">
                                <td class="fw-bold text-muted">{{ $events->firstItem() + $index }}</td>
                                <td><span class="badge bg-primary rounded-pill">{{ $event->akademi }}</span></td>
                                <td class="text-start">
                                    <div class="fw-bold">{{ $event->nama_kegiatan }}</div>
                                    <button type="button" onclick="syncDrive({{ $event->id }})" class="btn p-0 text-success fw-bold" style="font-size: 0.65rem;">
                                        <i class="fas fa-sync me-1" id="icon-{{ $event->id }}"></i> Sinkron & Analisis AI
                                    </button>
                                </td>
                                <td class="small">{{ $event->tanggal_mulai ? \Carbon\Carbon::parse($event->tanggal_mulai)->format('d M Y') : '-' }}</td>
                                <td class="small fw-bold">{{ $event->penanggung_jawab }}</td>
                                <td id="status-container-{{ $event->id }}">
                                    @php
                                        $done = 0; foreach($fields as $f) { if($event->$f == 'SUDAH') $done++; }
                                        $pct = round(($done / 11) * 100);
                                    @endphp
                                    <div style="cursor: pointer" data-bs-toggle="modal" data-bs-target="#modalAdmin{{ $event->id }}">
                                        <small class="fw-bold">{{ $pct }}%</small>
                                        <div class="progress" style="height: 4px; width: 50px; margin: auto;">
                                            <div class="progress-bar bg-primary" style="width: {{ $pct }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('admin.archives.index', ['search' => $event->nama_kegiatan]) }}"
                                    class="btn btn-warning btn-xs text-white rounded-pill px-2 py-1"
                                    style="font-size: 0.6rem;">
                                    <i class="fas fa-book me-1"></i> ARSIP
                                    </a>
                                </td>
                                <td><a href="{{ $event->lokasi }}" target="_blank" class="btn btn-success btn-xs rounded-pill px-2 py-1" style="font-size: 0.6rem;"><i class="fab fa-google-drive"></i></a></td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item text-primary" href="{{ route('dts.cetak', $event->id) }}"><i class="fas fa-file-pdf me-2"></i>Cetak</a></li>
                                            <li><a class="dropdown-item" href="{{ route('events.edit', $event->id) }}"><i class="fas fa-edit me-2"></i>Edit</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="9">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $events->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>

    {{-- SIDEBAR GEMINI (OFFCANVAS) --}}
    <div class="offcanvas offcanvas-end offcanvas-ai" tabindex="-1" id="aiSidebar" aria-labelledby="aiSidebarLabel">
        <div class="offcanvas-header bg-primary text-white">
            <h5 class="offcanvas-title fw-bold" id="aiSidebarLabel"><i class="fas fa-robot me-2"></i>Gemini AI Auditor</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="ai-sidebar-content" class="ai-content-box text-center">
                <div class="spinner-border text-primary" role="status" id="ai-loader" style="display:none;"></div>
                <div id="ai-result-text">
                    <p class="text-muted mt-5">Klik tombol sinkron pada kegiatan untuk memulai analisis.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL --}}
    @foreach($events as $event)
    <div class="modal fade" id="modalAdmin{{ $event->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0">
                <div class="modal-header bg-primary text-white py-2">
                    <small class="fw-bold">Rincian Dokumen</small>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="list-group list-group-flush small">
                        @foreach($fields as $f)
                        <li class="list-group-item d-flex justify-content-between py-1">
                            <span>{{ ucwords(str_replace(['adm_', '_'], ['', ' '], $f)) }}</span>
                            <span class="badge {{ $event->$f == 'SUDAH' ? 'bg-success' : 'bg-danger' }}">{{ $event->$f }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    function applyFilter(inputId, value) {
        document.getElementById(inputId).value = value;
        document.getElementById('filterForm').submit();
    }

    const aiSidebar = new bootstrap.Offcanvas(document.getElementById('aiSidebar'));

    function syncDrive(eventId) {
        const icon = document.getElementById('icon-' + eventId);
        const resultText = document.getElementById('ai-result-text');
        const loader = document.getElementById('ai-loader');

        // Buka Sidebar & Tampilkan Loading
        aiSidebar.show();
        icon.classList.add('fa-spin');
        loader.style.display = 'inline-block';
        resultText.innerHTML = '<p class="text-muted mt-2">Sedang memindai Drive & menganalisis PDF...</p>';

        axios.get(`/admin/events/sync-drive/${eventId}`)
            .then(res => {
                if (res.data.status === 'success') {
                    // Update Progress di Tabel
                    const container = document.getElementById('status-container-' + eventId);
                    const persen = res.data.percentage;
                    container.innerHTML = `<small class="fw-bold">${persen}%</small><div class="progress" style="height:4px;width:50px;margin:auto;"><div class="progress-bar bg-primary" style="width:${persen}%"></div></div>`;

                    // Tampilkan Hasil di Sidebar
                    let formatted = res.data.analysis
                        .replace(/\*\*(.*?)\*\*/g, '<b>$1</b>')
                        .replace(/\n/g, '<br>')
                        .replace(/\* /g, '• ');

                    resultText.innerHTML = `
                        <div class="alert alert-info border-0 small mb-3">Analisis ID #${eventId} Selesai</div>
                        <div class="text-start">${formatted}</div>
                        <button class="btn btn-sm btn-outline-primary w-100 mt-4" onclick="location.reload()">Refresh Data</button>
                    `;
                }
            })
            .catch(err => {
                resultText.innerHTML = '<p class="text-danger">Gagal menghubungi server AI.</p>';
            })
            .finally(() => {
                icon.classList.remove('fa-spin');
                loader.style.display = 'none';
            });
    }
    </script>
</body>
</html>
