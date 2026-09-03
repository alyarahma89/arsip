<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Laporan - BBPSDMP</title>
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/komdigi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --success-gradient: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
            --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
            --warning-gradient: linear-gradient(135deg, #f8961e 0%, #f3722c 100%);
            --primary-color: #4361ee;
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
        .sidebar-menu .nav-link { color: #64748b; border-radius: 10px; margin-bottom: 8px; padding: 12px 15px; transition: all 0.2s; }
        .sidebar-menu .nav-link:hover, .sidebar-menu .nav-link.active { background: var(--primary-gradient); color: white; transform: translateX(5px); }
        .sidebar-menu .nav-link i { width: 24px; text-align: center; margin-right: 10px; }

        .notification-badge { position: absolute; top: 10px; right: 15px; background: #f72585; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; }

        .main-content { margin-left: var(--sidebar-width); padding: 25px; min-height: 100vh; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 28px; font-weight: 700; color: #1e293b; margin: 0; }

        /* Cards */
        .analytics-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 24px; }
        .metric-card { background: white; border-radius: 12px; padding: 20px; border-left: 4px solid var(--primary-color); margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .metric-value { font-size: 32px; font-weight: 700; margin-bottom: 8px; color: #1e293b; }
        .metric-label { font-size: 14px; color: #64748b; margin-bottom: 12px; }

        /* Dropdown Notif & Filter */
        .dropdown-menu { margin-top: 10px !important; margin-left: 10px !important; border-radius: 12px !important; animation: fadeIn 0.2s ease-in-out; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; border: none; }
        .dropdown-item:hover { background-color: #f0f9ff; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }

        .menu-toggle { display: none; background: var(--primary-gradient); color: white; border: none; border-radius: 8px; padding: 8px 15px; margin-bottom: 20px; font-size: 1.2rem; }
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .menu-toggle { display: block !important; }
        }
    </style>
</head>
<body>

    {{-- DATA PENGAMBILAN AKADEMI DARI BLADE --}}
    @php
        $queryAkademi = \App\Models\Event::whereNotNull('akademi')
                        ->where('akademi', '!=', 'TIK') // Abaikan TIK
                        ->where('akademi', '!=', '-');

        if (isset($tahun) && $tahun) { $queryAkademi->whereYear('tanggal_mulai', $tahun); }
        if (isset($bulan) && $bulan) { $queryAkademi->whereMonth('tanggal_mulai', $bulan); }

        $dataAkademi = $queryAkademi->select('akademi', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                        ->groupBy('akademi')
                        ->pluck('total', 'akademi')
                        ->toArray();

        $akademiLabels = ['VSGA', 'DEA', 'TA', 'GTA', 'FGA'];
        $akademiValues = [];
        foreach($akademiLabels as $akd) {
            $akademiValues[] = $dataAkademi[$akd] ?? 0;
        }
    @endphp

    <div class="sidebar">
        {{-- LOGO SIDEBAR --}}
        <div class="sidebar-logo d-flex align-items-center mb-4 pb-3 border-bottom mx-3 mt-2">
            <img src="{{ asset('image/komdigi.png') }}" alt="Logo Komdigi"
                 style="width: 45px; height: auto; margin-right: 12px; object-fit: contain;">
            <div style="line-height: 1.2;">
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sistem Arsip</h6>
                <small class="text-muted" style="font-size: 0.7rem; font-weight: 600;">BBPSDMP KOMDIGI</small>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i> Data DTS <span class="badge bg-primary float-end">{{ $totalKegiatan }}</span></a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.assignments') }}"><i class="fas fa-tasks"></i> Data Lapgas <span class="badge bg-success float-end">{{ $laporanSudah + $laporanBelum }}</span></a></li>
                <li class="nav-item"><a class="nav-link active" href="{{ route('admin.analisis') }}"><i class="fas fa-chart-bar"></i> Analisis Laporan</a></li>

                {{-- MENU NOTIFIKASI --}}
                @php $totalNotif = $laporanBelum + $dtsBelum; @endphp
                <li class="nav-item dropdown position-relative">
                    <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-bell"></i> Notifikasi
                        @if($totalNotif > 0) <span class="notification-badge">{{ $totalNotif }}</span> @endif
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="notifDropdown" style="width: 260px;">
                        <li><h6 class="dropdown-header text-uppercase small fw-bold text-muted">Perlu Tindakan</h6></li>
                        <li>
                            <a class="dropdown-item rounded p-2 d-flex align-items-center" href="{{ route('admin.assignments', ['filter' => 'pending']) }}">
                                <div class="bg-danger bg-opacity-10 p-2 rounded-circle me-3 text-danger"><i class="fas fa-user-clock"></i></div>
                                <div><div class="fw-bold text-dark">{{ $laporanBelum }} Pegawai</div><div class="small text-muted">Belum lapor tugas</div></div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded p-2 d-flex align-items-center mt-1" href="{{ route('admin.events', ['filter' => 'incomplete']) }}">
                                <div class="bg-warning bg-opacity-10 p-2 rounded-circle me-3 text-warning"><i class="fas fa-file-signature"></i></div>
                                <div><div class="fw-bold text-dark">{{ $dtsBelum }} Kegiatan</div><div class="small text-muted">Admin belum lengkap</div></div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.history') }}"><i class="fas fa-history"></i> Riwayat Upload</a></li>
            </ul>
        </div>

        <div class="mt-auto p-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger w-100" type="submit"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
            </form>
            <div class="text-center mt-3"><p class="text-muted small mb-0">© 2026 BBPSDMP</p></div>
        </div>
    </div>

    <div class="main-content">
        <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i></button>

        {{-- HEADER & IKON FILTER BARU --}}
        <div class="header align-items-start">
            <div>
                <h1>Analisis Statistik</h1>
                <p class="text-muted mb-0 mt-1">
                    Menampilkan Data:
                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill ms-2">
                        {{ request('bulan') ? \Carbon\Carbon::create()->month(request('bulan'))->translatedFormat('F') : 'Semua Bulan' }} -
                        {{ request('tahun') ? request('tahun') : 'Semua Tahun' }}
                    </span>
                </p>
            </div>

            <form action="{{ route('admin.analisis') }}" method="GET" id="filterForm" class="d-flex gap-2 mt-2">
                <input type="hidden" name="bulan" id="inputMonth" value="{{ request('bulan') }}">
                <input type="hidden" name="tahun" id="inputYear" value="{{ request('tahun') }}">

                {{-- 1. Ikon Filter Bulan --}}
                <div class="dropdown">
                    <button class="btn btn-white border shadow-sm p-0 position-relative text-secondary" type="button" data-bs-toggle="dropdown" title="Filter Bulan" style="border-radius: 10px; background: white; width: 42px; height: 42px; display:flex; align-items:center; justify-content:center;">
                        <i class="far fa-calendar-alt fs-5" style="color: var(--primary-color);"></i>
                        @if(request('bulan')) <span class="position-absolute top-0 start-100 translate-middle bg-danger border border-light rounded-circle" style="width: 12px; height: 12px;"></span> @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 12px; min-width: 160px; max-height: 250px; overflow-y: auto;">
                        <li><a class="dropdown-item fw-bold {{ !request('bulan') ? 'text-primary' : 'text-muted' }}" href="#" onclick="applyFilter('inputMonth', '')"><i class="fas fa-calendar-alt me-2"></i> Semua Bulan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        @foreach(range(1,12) as $m)
                            <li><a class="dropdown-item {{ request('bulan') == $m ? 'active bg-primary text-white' : '' }}" href="#" onclick="applyFilter('inputMonth', '{{ $m }}')">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- 2. Ikon Filter Tahun --}}
                <div class="dropdown">
                    <button class="btn btn-white border shadow-sm p-0 position-relative text-secondary" type="button" data-bs-toggle="dropdown" title="Filter Tahun" style="border-radius: 10px; background: white; width: 42px; height: 42px; display:flex; align-items:center; justify-content:center;">
                        <i class="fas fa-calendar-check fs-5" style="color: var(--primary-color);"></i>
                        @if(request('tahun')) <span class="position-absolute top-0 start-100 translate-middle bg-danger border border-light rounded-circle" style="width: 12px; height: 12px;"></span> @endif
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0" style="border-radius: 12px; min-width: 160px;">
                        <li><a class="dropdown-item fw-bold {{ !request('tahun') ? 'text-primary' : 'text-muted' }}" href="#" onclick="applyFilter('inputYear', '')"><i class="fas fa-calendar-check me-2"></i> Semua Tahun</a></li>
                        <li><hr class="dropdown-divider"></li>
                        @foreach(range(date('Y'), date('Y')-3) as $y)
                            <li><a class="dropdown-item {{ request('tahun') == $y ? 'active bg-primary text-white' : '' }}" href="#" onclick="applyFilter('inputYear', '{{ $y }}')">{{ $y }}</a></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Tombol Reset --}}
                @if(request('bulan') || request('tahun'))
                    <a href="{{ route('admin.analisis') }}" class="btn btn-outline-danger d-flex align-items-center shadow-sm" style="border-radius: 10px; height: 42px;" title="Reset Filter">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- METRIC CARDS --}}
        <div class="row">
            <div class="col-md-3 col-6"><div class="metric-card" style="border-left-color: #f8961e;"><div class="metric-value">{{ $totalKegiatan }}</div><div class="metric-label">Total Kegiatan DTS</div></div></div>
            <div class="col-md-3 col-6"><div class="metric-card" style="border-left-color: #8b5cf6;"><div class="metric-value">{{ $totalPegawai }}</div><div class="metric-label">Total Pegawai</div></div></div>
            <div class="col-md-3 col-6"><div class="metric-card" style="border-left-color: #4cc9f0;"><div class="metric-value text-success">{{ $laporanSudah }}</div><div class="metric-label">Sudah Lapor</div></div></div>
            <div class="col-md-3 col-6"><div class="metric-card" style="border-left-color: #f72585;"><div class="metric-value text-danger">{{ $laporanBelum }}</div><div class="metric-label">Belum Lapor</div></div></div>
        </div>

        {{-- CHARTS ROW 1 --}}
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="analytics-card h-100 d-flex flex-column">
                    <div class="card-title mb-3"><i class="fas fa-chart-pie text-success"></i> Kelengkapan DTS</div>
                    <div style="height: 250px; flex-grow: 1;"><canvas id="dtsChart"></canvas></div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-around text-center">
                        <div><h5 class="fw-bold mb-0 text-success">{{ $dtsLengkap }}</h5><small class="text-muted" style="font-size: 0.75rem;">Lengkap</small></div>
                        <div class="border-end"></div>
                        <div><h5 class="fw-bold mb-0 text-danger">{{ $dtsBelum }}</h5><small class="text-muted" style="font-size: 0.75rem;">Belum</small></div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="analytics-card h-100 d-flex flex-column">
                    <div class="card-title mb-3 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-chart-line text-info"></i> Trend Laporan Bulanan</span>
                    </div>
                    <div style="height: 250px; flex-grow: 1;"><canvas id="trendChart"></canvas></div>
                    <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between text-muted small">
                        <div><i class="fas fa-info-circle me-1"></i> Data diupdate secara real-time.</div>
                        <div><span class="me-3"><i class="fas fa-circle text-info me-1" style="font-size: 8px;"></i>Masuk</span><span><i class="fas fa-circle text-danger me-1" style="font-size: 8px;"></i>Belum</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CHARTS ROW 2 (DIBAGI 3 KOLOM) --}}
        <div class="row">
            {{-- Rasio Kepatuhan --}}
            <div class="col-md-4 mb-4">
                <div class="analytics-card h-100 d-flex flex-column justify-content-center align-items-center">
                    <div class="card-title w-100 text-center mb-3"><i class="fas fa-percentage text-primary"></i> Rasio Kepatuhan</div>
                    <div style="position: relative; height: 160px; width: 160px;">
                        <canvas id="rasioChart"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center;">
                            <h2 class="fw-bold mb-0 text-primary" id="rasioText">0%</h2><small class="text-muted" style="font-size: 0.7rem;">Selesai</small>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-top w-100 text-center text-muted small">
                        <div class="row">
                            <div class="col-6 border-end"><strong class="d-block text-dark">{{ $laporanSudah }}</strong> Selesai</div>
                            <div class="col-6"><strong class="d-block text-dark">{{ $laporanBelum }}</strong> Pending</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GRAFIK PERSENTASE AKADEMI (Pie Chart) --}}
            <div class="col-md-4 mb-4">
                <div class="analytics-card h-100 d-flex flex-column">
                    <div class="card-title mb-3 text-center"><i class="fas fa-graduation-cap text-warning"></i> Persentase Akademi</div>
                    <div style="height: 180px; flex-grow: 1; display: flex; justify-content: center;">
                        <canvas id="akademiChart"></canvas>
                    </div>
                    <div class="mt-3 pt-3 border-top text-center text-muted small">
                        <i class="fas fa-info-circle me-1"></i> Sorot grafik untuk persentase (%).
                    </div>
                </div>
            </div>

            {{-- Top Pegawai --}}
            <div class="col-md-4 mb-4">
                <div class="analytics-card h-100 d-flex flex-column">
                    <div class="card-title mb-3 d-flex justify-content-between">
                        <span><i class="fas fa-trophy text-warning"></i> Top 5 Pegawai</span>
                    </div>
                    <div style="height: 200px; flex-grow: 1;"><canvas id="topPegawaiChart"></canvas></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Script untuk memproses klik dropdown filter --}}
    <script>
        function applyFilter(inputId, value) {
            event.preventDefault();
            document.getElementById(inputId).value = value;
            document.getElementById('filterForm').submit();
        }
    </script>

    <script>
        document.getElementById('menuToggle').addEventListener('click', function() { document.querySelector('.sidebar').classList.toggle('active'); });
        const colors = { primary: '#4361ee', success: '#4cc9f0', warning: '#f8961e', danger: '#f72585' };

        // 1. Chart Kelengkapan DTS
        new Chart(document.getElementById('dtsChart'), {
            type: 'doughnut',
            data: { labels: ['Lengkap', 'Belum Lengkap'], datasets: [{ data: [{{ $dtsLengkap }}, {{ $dtsBelum }}], backgroundColor: [colors.success, colors.danger], borderWidth: 0 }] },
            options: { cutout: '70%', plugins: { legend: { position: 'bottom' } } }
        });

        // 2. Chart Trend Laporan
        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    { label: 'Laporan Masuk', data: @json(array_values($trendSudah)), borderColor: colors.success, backgroundColor: 'rgba(76, 201, 240, 0.1)', fill: true, tension: 0.4 },
                    { label: 'Belum Lapor', data: @json(array_values($trendBelum)), borderColor: colors.danger, backgroundColor: 'rgba(247, 37, 133, 0.05)', fill: true, tension: 0.4 }
                ]
            },
            options: { maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });

        // 3. Chart Top Pegawai
        const topNames = @json($topPegawai->pluck('nama_pegawai'));
        const topTotals = @json($topPegawai->pluck('total'));
        new Chart(document.getElementById('topPegawaiChart'), {
            type: 'bar',
            data: { labels: topNames, datasets: [{ label: 'Laporan Selesai', data: topTotals, backgroundColor: ['#4361ee', '#4cc9f0', '#4895ef', '#560bad', '#3f37c9'], borderRadius: 5, barThickness: 15 }] },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, grid: { display: false } }, y: { grid: { display: false }, ticks: { font: { size: 10 } } } } }
        });

        // 4. Chart Rasio Kepatuhan
        const sudahLapor = {{ $laporanSudah }}; const belumLapor = {{ $laporanBelum }}; const totalTugas = sudahLapor + belumLapor;
        document.getElementById('rasioText').innerText = (totalTugas > 0 ? Math.round((sudahLapor / totalTugas) * 100) : 0) + "%";

        new Chart(document.getElementById('rasioChart'), {
            type: 'doughnut',
            data: { labels: ['Sudah Lapor', 'Belum'], datasets: [{ data: [sudahLapor, belumLapor], backgroundColor: ['#4361ee', '#f1f5f9'], borderWidth: 0, borderRadius: 20, cutout: '85%' }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { enabled: false } }, animation: { animateScale: true, animateRotate: true } }
        });

        // 5. CHART AKADEMI (Pie Chart)
        new Chart(document.getElementById('akademiChart'), {
            type: 'pie',
            data: {
                labels: @json($akademiLabels),
                datasets: [{
                    data: @json($akademiValues),
                    backgroundColor: ['#00AEEF', '#4361ee', '#4cc9f0', '#f8961e', '#f72585'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, font: { size: 10 } } },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let value = context.raw;
                                let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return ' ' + context.label + ': ' + value + ' Kegiatan (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
