<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laporan Tugas (Lapgas) - Komdigi</title>
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/komdigi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    {{-- TAMBAHAN: SweetAlert2 untuk Loading AI yang cantik --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root { --komdigi-blue-dark: #0E2C6C; --komdigi-blue-light: #00AEEF; --komdigi-accent: #FDB913; --komdigi-bg: #F4F7F9; }
        body { background-color: var(--komdigi-bg); font-family: 'Segoe UI', sans-serif; color: #334155; }
        .container-fluid { max-width: 1400px; }
        .header-section { background: linear-gradient(135deg, var(--komdigi-blue-dark) 0%, #1a4299 100%); color: white; border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 2rem; position: relative; box-shadow: 0 8px 20px rgba(14, 44, 108, 0.2); }
        .header-section::after { content: ''; position: absolute; top: 0; right: 0; width: 150px; height: 100%; background: linear-gradient(to bottom left, var(--komdigi-blue-light), transparent); opacity: 0.2; pointer-events: none; border-radius: 0 12px 12px 0; }
        .btn-header { background: rgba(255, 255, 255, 0.2); color: white; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(5px); transition: 0.3s; font-weight: 500; }
        .btn-header:hover { background: white; color: var(--komdigi-blue-dark); transform: translateY(-2px); }
        .btn-header-add { background: var(--komdigi-accent); color: var(--komdigi-blue-dark); border: none; font-weight: 600; transition: 0.3s; }
        .btn-header-add:hover { background: #e6a800; color: var(--komdigi-blue-dark); transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,0,0,0.15); }
        .stat-card-link { text-decoration: none; color: inherit; display: block; height: 100%; }
        .stats-card { background: white; border-radius: 10px; padding: 1.25rem 1.5rem; border-left: 4px solid var(--komdigi-blue-light); transition: all 0.3s; height: 100%; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); }
        .stats-card:hover { transform: translateY(-5px); background-color: rgba(255, 255, 255, 0.2); }
        .stats-card.active-card { background-color: rgba(255, 255, 255, 0.25); border: 1px solid var(--komdigi-accent); box-shadow: 0 0 15px rgba(253, 185, 19, 0.3); }
        .table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0; }
        .table thead th { background: #f8fafc; color: var(--komdigi-blue-dark); font-weight: 700; text-transform: uppercase; font-size: 0.85rem; border-bottom: 2px solid #e2e8f0; padding: 1rem; }
        .search-container { background: white; border-radius: 10px; padding: 1.5rem; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03); margin-bottom: 1.5rem; border: 1px solid #e2e8f0; }
        .pegawai-avatar { width: 40px; height: 40px; background: linear-gradient(135deg, var(--komdigi-blue-dark), var(--komdigi-blue-light)); color: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .date-badge { background-color: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; padding: 0.4rem 0.8rem; border-radius: 6px; font-weight: 500; font-size: 0.85rem; }
        .pagination .page-link { color: var(--komdigi-blue-dark); border: none; margin: 0 2px; }
        .pagination .page-item.active .page-link { background-color: var(--komdigi-blue-dark); color: white; }
        .bg-status-completed { background-color: #d1fae5; color: #065f46; }
        .bg-status-pending { background-color: #fffbeb; color: #92400e; }
        .dropdown-menu { border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border-radius: 12px; }
        .dropdown-item:active { background-color: var(--komdigi-blue-dark); }
        .cursor-pointer { cursor: pointer; }
    </style>
</head>
<body class="p-4">

    <div class="container-fluid">
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="mb-1 fw-bold"><i class="fas fa-clipboard-check me-2"></i> Daftar Laporan Tugas</h2>
                    <p class="mb-0 opacity-75">Monitoring kinerja dan pelaporan tugas pegawai</p>
                </div>

                <div class="d-flex align-items-center">
                    <a href="{{ route('admin.assignments.create') }}" class="btn btn-header-add btn-sm px-3 py-2 rounded-pill me-2 shadow-sm text-decoration-none">
                        <i class="fas fa-plus me-1"></i> Tambah Laporan
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-header btn-sm px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>

            <div class="row mt-4 g-3">
                <div class="col-md-3">
                    <a href="{{ route('admin.assignments') }}" class="stat-card-link">
                        <div class="stats-card border-0 bg-white bg-opacity-10 text-white backdrop-blur {{ !request('filter') ? 'active-card' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><h4 class="mb-0 fw-bold">{{ \App\Models\Assignment::count() }}</h4><small class="opacity-75">Total Laporan</small></div>
                                <div class="bg-white bg-opacity-25 rounded p-2"><i class="fas fa-file-alt fs-4"></i></div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.assignments', ['filter' => 'completed']) }}" class="stat-card-link">
                        <div class="stats-card border-0 bg-white bg-opacity-10 text-white backdrop-blur {{ request('filter') == 'completed' ? 'active-card' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><h4 class="mb-0 fw-bold">{{ \App\Models\Assignment::where('status_laporan', true)->count() }}</h4><small class="opacity-75">Sudah Dilaporkan</small></div>
                                <div class="bg-success bg-opacity-75 rounded p-2 text-white"><i class="fas fa-check fs-4"></i></div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.assignments', ['filter' => 'pending']) }}" class="stat-card-link">
                        <div class="stats-card border-0 bg-white bg-opacity-10 text-white backdrop-blur {{ request('filter') == 'pending' ? 'active-card' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><h4 class="mb-0 fw-bold">{{ \App\Models\Assignment::where('status_laporan', false)->count() }}</h4><small class="opacity-75">Belum Dilaporkan</small></div>
                                <div class="bg-warning bg-opacity-75 rounded p-2 text-dark"><i class="fas fa-clock fs-4"></i></div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.assignments', ['filter' => 'has_proof']) }}" class="stat-card-link">
                        <div class="stats-card border-0 bg-white bg-opacity-10 text-white backdrop-blur {{ request('filter') == 'has_proof' ? 'active-card' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><h4 class="mb-0 fw-bold">{{ \App\Models\Assignment::where('link_bukti', '!=', '-')->where('link_bukti', '!=', '')->count() }}</h4><small class="opacity-75">Memiliki Bukti</small></div>
                                <div class="bg-info bg-opacity-75 rounded p-2 text-white"><i class="fas fa-link fs-4"></i></div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="search-container">
            <form action="{{ route('admin.assignments') }}" method="GET" id="filterForm">
                @if(request('filter')) <input type="hidden" name="filter" value="{{ request('filter') }}"> @endif

                <input type="hidden" name="bulan" id="inputMonth" value="{{ request('bulan') }}">
                <input type="hidden" name="tahun" id="inputYear" value="{{ request('tahun') }}">

                <div class="row g-2 align-items-center">
                    <div class="col-auto d-flex gap-2">
                        <div class="dropdown">
                            <button class="btn btn-white border shadow-sm p-0 position-relative text-secondary" type="button" data-bs-toggle="dropdown" title="Filter Bulan" style="border-radius: 10px; background: white; width: 42px; height: 42px; display:flex; align-items:center; justify-content:center;">
                                <i class="far fa-calendar-alt fs-5" style="color: var(--komdigi-blue-dark);"></i>
                                @if(request('bulan')) <span class="position-absolute top-0 start-100 translate-middle bg-danger border border-light rounded-circle" style="width: 12px; height: 12px;"></span> @endif
                            </button>
                            <ul class="dropdown-menu shadow-lg border-0" style="border-radius: 12px; min-width: 160px; max-height: 250px; overflow-y: auto;">
                                <li><a class="dropdown-item fw-bold {{ !request('bulan') ? 'text-primary' : 'text-muted' }}" href="#" onclick="applyFilter('inputMonth', '')"><i class="fas fa-calendar-alt me-2"></i> Semua Bulan</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach(range(1,12) as $m)
                                    <li><a class="dropdown-item {{ request('bulan') == $m ? 'active bg-primary text-white' : '' }}" href="#" onclick="applyFilter('inputMonth', '{{ $m }}')">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</a></li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-white border shadow-sm p-0 position-relative text-secondary" type="button" data-bs-toggle="dropdown" title="Filter Tahun" style="border-radius: 10px; background: white; width: 42px; height: 42px; display:flex; align-items:center; justify-content:center;">
                                <i class="fas fa-calendar-check fs-5" style="color: var(--komdigi-blue-dark);"></i>
                                @if(request('tahun')) <span class="position-absolute top-0 start-100 translate-middle bg-danger border border-light rounded-circle" style="width: 12px; height: 12px;"></span> @endif
                            </button>
                            <ul class="dropdown-menu shadow-lg border-0" style="border-radius: 12px; min-width: 160px;">
                                <li><a class="dropdown-item fw-bold {{ !request('tahun') ? 'text-primary' : 'text-muted' }}" href="#" onclick="applyFilter('inputYear', '')"><i class="fas fa-calendar-check me-2"></i> Semua Tahun</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach(range(date('Y'), date('Y')-3) as $y)
                                    <li><a class="dropdown-item {{ request('tahun') == $y ? 'active bg-primary text-white' : '' }}" href="#" onclick="applyFilter('inputYear', '{{ $y }}')">{{ $y }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="col">
                        <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                            <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari nama / kegiatan..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary px-4 fw-bold" style="background-color: var(--komdigi-blue-dark); border-color: var(--komdigi-blue-dark);">Cari</button>
                        </div>
                    </div>

                    <div class="col-auto d-flex gap-2">
                        @if(request('filter') || request('search') || request('bulan') || request('tahun'))
                            <a href="{{ route('admin.assignments') }}" class="btn btn-outline-danger d-flex align-items-center shadow-sm" style="border-radius: 10px; height: 42px;">
                                <i class="fas fa-times me-1"></i> Reset
                            </a>
                        @endif

                        <div class="dropdown">
                            <button class="btn btn-success d-flex align-items-center shadow-sm text-white" type="button" data-bs-toggle="dropdown" style="background-color: #10b981; border: none; border-radius: 10px; height: 42px;">
                                <i class="fas fa-file-export me-2"></i> Export
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 12px;">
                                <li><h6 class="dropdown-header">Pilih Format:</h6></li>
                                <li><a class="dropdown-item" href="{{ route('admin.assignments.export.pdf', request()->all()) }}"><i class="fas fa-file-pdf text-danger me-2"></i> Export PDF</a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.assignments.export.excel', request()->all()) }}"><i class="fas fa-file-excel text-success me-2"></i> Export Excel</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><button type="button" class="dropdown-item" onclick="exportKeGambar()"><i class="fas fa-image text-warning me-2"></i> Export PNG</button></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

            <script>
                function applyFilter(inputId, value) {
                    event.preventDefault();
                    document.getElementById(inputId).value = value;
                    document.getElementById('filterForm').submit();
                }
            </script>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 bg-success bg-opacity-10 text-success shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- TABLE DATA --}}
        <div class="table-container shadow-sm" style="min-height: 400px;">
            <div class="table-responsive">
                <table class="table mb-0 align-middle table-hover">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">No</th>
                            <th style="width: 20%;">Pegawai</th>
                            <th style="width: 15%;">Tanggal</th>
                            <th style="width: 25%;">Kegiatan</th>
                            <th class="text-center" style="width: 10%;">Status Pekerjaan</th>
                            <th class="text-center" style="width: 15%;">Kelengkapan AI</th>
                            <th class="text-center" style="width: 10%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $index => $data)
                        @php
                            $isSelesai = $data->status_laporan == 1;
                            $isOverdue = !$isSelesai && \Carbon\Carbon::parse($data->tanggal)->diffInDays(now(), false) > -2;
                        @endphp
                        <tr style="{{ $isOverdue ? 'background-color: #fef2f2;' : '' }}">
                            <td class="text-center text-muted fw-bold">{{ $assignments->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="pegawai-avatar me-3 flex-shrink-0">{{ strtoupper(substr($data->nama_pegawai, 0, 1)) }}</div>
                                    <div class="fw-bold text-dark">{{ $data->nama_pegawai }}</div>
                                </div>
                            </td>
                            <td><span class="date-badge"><i class="far fa-calendar me-1"></i> {{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d M Y') : '-' }}</span></td>
                            <td><div class="text-wrap" style="max-width: 250px;">{{ $data->kegiatan }}</div></td>
                            <td class="text-center">
                                @if($data->status_laporan)
                                    <span class="badge bg-status-completed border border-success border-opacity-25 rounded-pill px-3"><i class="fas fa-check me-1"></i> Selesai</span>
                                @else
                                    <span class="badge bg-status-pending border border-warning border-opacity-25 rounded-pill px-3"><i class="fas fa-hourglass-half me-1"></i> Pending</span>
                                @endif
                            </td>

                            {{-- KOLOM AI BARU --}}
                            <td class="text-center">
                                @if(isset($data->status_adm) && $data->status_adm == 'LENGKAP')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-1 rounded-pill cursor-pointer"
                                        onclick="syncLapgas('{{ $data->id }}')" title="{{ $data->catatan_ai }}">
                                        <i class="fas fa-check-circle me-1"></i> LENGKAP
                                    </span>
                                @elseif(isset($data->status_adm) && $data->status_adm == 'BELUM')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1 rounded-pill cursor-pointer"
                                        onclick="syncLapgas('{{ $data->id }}')" title="{{ $data->catatan_ai }}">
                                        <i class="fas fa-times-circle me-1"></i> BELUM
                                    </span>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-1 rounded-pill cursor-pointer"
                                        onclick="syncLapgas('{{ $data->id }}')">
                                        <i class="fas fa-robot me-1"></i> Cek AI
                                    </span>
                                @endif
                            </td>

                            {{-- KOLOM AKSI --}}
                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle border shadow-sm" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                        <li><h6 class="dropdown-header">Opsi Laporan</h6></li>

                                        {{-- Menu Buka Bukti & Sinkron AI --}}
                                        @if($data->link_bukti && $data->link_bukti != '-')
                                            <li>
                                                <a class="dropdown-item" href="{{ str_contains(strtolower($data->link_bukti), 'http') ? $data->link_bukti : '#' }}" target="_blank">
                                                    <i class="fas fa-external-link-alt text-primary me-2"></i> Buka Drive
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                        @endif

                                        <li>
                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $data->id }}">
                                                <i class="fas fa-edit text-warning me-2"></i> Edit Data
                                            </a>
                                        </li>

                                        <li>
                                            <form action="{{ route('admin.assignments.delete', $data->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash-alt me-2"></i> Hapus
                                                </button>
                                            </form>
                                        </li>

                                        <a href="{{ route('dts.cetak', $data->id) }}" class="btn btn-primary btn-sm mt-1 w-100 shadow-sm" style="border-radius: 0 0 10px 10px;">
                                            <i class="fas fa-file-pdf me-1"></i> Cetak Laporan
                                        </a>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assignments->hasPages())
            <div class="pagination-simple p-3 d-flex justify-content-between align-items-center bg-white border-top">
                <div class="text-muted small">Halaman {{ $assignments->currentPage() }} dari {{ $assignments->lastPage() }}</div>
                <div>{{ $assignments->links('pagination::bootstrap-5') }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- MODAL EDIT (LOOPING) --}}
    @foreach($assignments as $data)
    <div class="modal fade" id="modalEdit{{ $data->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-dark">Edit Laporan Tugas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.assignments.update', $data->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nama Pegawai</label>
                            <input type="text" name="nama_pegawai" class="form-control" value="{{ $data->nama_pegawai }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control" value="{{ \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Kegiatan</label>
                            <textarea name="kegiatan" class="form-control" rows="3" required>{{ $data->kegiatan }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Link Bukti (Google Drive)</label>
                            <input type="text" name="link_bukti" class="form-control" value="{{ $data->link_bukti }}">
                            <small class="text-muted">Masukkan link folder Drive untuk bisa dianalisis AI.</small>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="status{{ $data->id }}" name="status_laporan" value="1" {{ $data->status_laporan ? 'checked' : '' }}>
                            <label class="form-check-label" for="status{{ $data->id }}">Tandai Selesai</label>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    {{-- SCRIPT SINKRONISASI AI & EXPORT --}}
    <script>
        function exportKeGambar() {
            const element = document.querySelector('.table-container');
            html2canvas(element, { scale: 2 }).then(canvas => {
                let link = document.createElement('a');
                link.download = 'Data_Laporan_Tugas.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }

        // Script Pemicu Analisis AI
        function syncLapgas(id) {
            Swal.fire({
                title: 'Menganalisis Drive...',
                text: 'Gemini sedang mengecek kelengkapan berkas Lapgas',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            // Ganti route di bawah ini sesuai dengan route web.php kamu
            fetch(`/admin/assignments/sync/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Berhasil!', data.result.catatan_ai || 'Analisis selesai.', 'success')
                    .then(() => { location.reload(); });
                } else {
                    Swal.fire('Gagal', data.message, 'error');
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire('Error', 'Terjadi kesalahan saat menghubungi server.', 'error');
            });
        }
    </script>
</body>
</html>
