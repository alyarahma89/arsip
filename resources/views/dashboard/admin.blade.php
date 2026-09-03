<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Arsiparis - BBPSDM</title>
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/komdigi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    {{-- Tambahkan library Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --danger-gradient: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
            --warning-gradient: linear-gradient(135deg, #f8961e 0%, #f3722c 100%);
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

        /* Main Content */
        .main-content { margin-left: var(--sidebar-width); padding: 20px; min-height: 100vh; }
        .dashboard-header { background: white; border-radius: 15px; padding: 1.5rem; margin-bottom: 2rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border-left: 5px solid #4361ee; }

        /* Cards */
        .stat-card { border-radius: 15px; border: none; overflow: hidden; transition: transform 0.3s, box-shadow 0.3s; height: 100%; background: white; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important; }
        .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; margin-bottom: 1rem; }

        /* Progress Bar */
        .progress-container { background: white; border-radius: 15px; padding: 1.5rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); height: 100%; }
        .progress { height: 12px; border-radius: 6px; background-color: #e2e8f0; }

        /* Notifications */
        .notification-badge { position: absolute; top: 10px; right: 15px; background: #f72585; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; }

        /* Chatbot ARAI Styling */
        #arai-btn { position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; background: var(--primary-gradient); color: white; border-radius: 50%; border: none; box-shadow: 0 10px 25px rgba(67, 97, 238, 0.4); font-size: 28px; cursor: move; z-index: 9999; display: flex; justify-content: center; align-items: center; }
        #arai-chat-window { position: fixed; bottom: 100px; right: 30px; width: 350px; height: 450px; background: white; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); display: flex; flex-direction: column; z-index: 9999; overflow: hidden; opacity: 0; pointer-events: none; transform: translateY(20px); transition: all 0.3s ease; }
        #arai-chat-window.active { opacity: 1; pointer-events: auto; transform: translateY(0); }
        .arai-header { background: var(--primary-gradient); color: white; padding: 15px 20px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; cursor: grab; }
        .arai-body { flex-grow: 1; padding: 15px; overflow-y: auto; background-color: #f8fafc; display: flex; flex-direction: column; gap: 10px; }
        .chat-bubble { max-width: 80%; padding: 10px 15px; border-radius: 15px; font-size: 0.9rem; }
        .chat-ai { background: #e0e7ff; color: #1e3a8a; align-self: flex-start; border-bottom-left-radius: 0; }
        .chat-user { background: #4361ee; color: white; align-self: flex-end; border-bottom-right-radius: 0; }
        .arai-footer { padding: 15px; background: white; border-top: 1px solid #e2e8f0; }
        .arai-input-group { display: flex; gap: 10px; }
        .arai-input { flex-grow: 1; border: 1px solid #cbd5e1; border-radius: 20px; padding: 8px 15px; outline: none; }
        .arai-send { background: #4361ee; color: white; border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; }

        /* Filter Tahun Styling */
        .btn-filter-tahun {
            background: white; border: 1px solid #e2e8f0; color: #4361ee;
            font-weight: 600; border-radius: 10px; padding: 8px 15px;
            transition: all 0.3s;
        }
        .btn-filter-tahun:hover { border-color: #4361ee; background-color: #f0f3ff; }
    </style>
</head>
<body>

    {{-- SIDEBAR --}}
    <div class="sidebar">
        <div class="sidebar-logo d-flex align-items-center mb-4 pb-3 border-bottom mx-3 mt-2">
            <img src="{{ asset('image/komdigi.png') }}" alt="Logo Komdigi" style="width: 45px; height: auto; margin-right: 12px;">
            <div style="line-height: 1.2;">
                <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.95rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sistem Arsip</h6>
                <small class="text-muted" style="font-size: 0.7rem; font-weight: 600;">BBPSDM KOMDIGI</small>
            </div>
        </div>

        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link active" href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i> Data DTS</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.assignments') }}"><i class="fas fa-tasks"></i> Data Lapgas</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.archives.index') }}"><i class="fas fa-file-invoice"></i> Buku Induk Arsip</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.analisis') }}"><i class="fas fa-chart-bar"></i> Analisis Laporan</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admin.history') }}"><i class="fas fa-history"></i> Riwayat Upload</a></li>
            </ul>
        </div>

        <div class="mt-auto p-3 border-top mx-3 mb-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="btn btn-danger w-100 rounded-pill fw-bold" type="submit"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
            </form>
            <div class="text-center mt-3"><p class="text-muted small mb-0" style="font-size: 0.7rem;">© 2026 BBPSDM KOMDIGI</p></div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="main-content">
        <button class="menu-toggle mb-3" id="menuToggle" style="display:none;"><i class="fas fa-bars"></i></button>

        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">👋 Selamat Datang, Admin!</h2>
                <p class="text-muted mb-0">Statistik real-time administrasi BBPSDM KOMDIGI.</p>
            </div>
            <div class="badge bg-light text-dark p-2 border"><i class="fas fa-clock me-1 text-primary"></i> <span id="currentTime"></span></div>
        </div>

        {{-- ROW 1: METRIC CARDS --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card p-4 border-start border-primary border-5">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-calendar-alt"></i></div>
                    <h6 class="text-muted small fw-bold">TOTAL KEGIATAN DTS</h6>
                    <h2 class="fw-bold mb-0">{{ $totalKegiatan ?? 0 }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-4 border-start border-info border-5">
                    <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="fas fa-users"></i></div>
                    <h6 class="text-muted small fw-bold">TOTAL PEGAWAI</h6>
                    <h2 class="fw-bold mb-0">{{ $totalPegawai ?? 0 }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-4 border-start border-success border-5">
                    <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle"></i></div>
                    <h6 class="text-muted small fw-bold">LAPGAS SELESAI</h6>
                    <h2 class="fw-bold mb-0">{{ $laporanSudah ?? 0 }}</h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card p-4 border-start border-danger border-5">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-exclamation-circle"></i></div>
                    <h6 class="text-muted small fw-bold">LAPGAS PENDING</h6>
                    <h2 class="fw-bold mb-0 text-danger">{{ $laporanBelum ?? 0 }}</h2>
                </div>
            </div>
        </div>

        {{-- ROW 2: GRAFIK STATISTIK (DENGAN DROPDOWN TAHUN) --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="stat-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Visualisasi Analisis Laporan</h5>

                        {{-- DROPDOWN PILIH TAHUN --}}
                        <div class="dropdown">
                            <button class="btn btn-filter-tahun dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="far fa-calendar-alt me-2"></i> Tahun: {{ $selectedYear == 'all' ? 'Semua' : $selectedYear }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; min-width: 150px;">
                                <li><a class="dropdown-item" href="{{ route('dashboard', ['year' => 'all']) }}">Semua Tahun</a></li>
                                <li><hr class="dropdown-divider"></li>
                                @foreach($availableYears as $year)
                                    <li><a class="dropdown-item {{ $selectedYear == $year ? 'active bg-primary' : '' }}" href="{{ route('dashboard', ['year' => $year]) }}">{{ $year }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="row g-4">
                        {{-- Grafik Perbandingan Lapgas --}}
                        <div class="col-lg-4 text-center border-end">
                            <h6 class="fw-bold text-muted mb-3">Persentase Pelaporan</h6>
                            <div style="height: 250px; position: relative;">
                                <canvas id="lapgasChart"></canvas>
                            </div>
                        </div>

                        {{-- Grafik Kelengkapan Berkas DTS --}}
                        <div class="col-lg-8">
                            <h6 class="fw-bold text-muted mb-3">Status Administrasi DTS (Tahun {{ $selectedYear == 'all' ? 'Global' : $selectedYear }})</h6>
                            <div style="height: 250px;">
                                <canvas id="dtsBerkasChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROW 3: PROGRESS & AKTIVITAS --}}
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="progress-container">
                    <h5 class="fw-bold mb-3">Target Pelaporan Pegawai</h5>
                    @php
                        $total = ($laporanSudah ?? 0) + ($laporanBelum ?? 0);
                        $progress = $total > 0 ? ($laporanSudah / $total) * 100 : 0;
                    @endphp
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pencapaian: {{ round($progress, 1) }}%</span>
                        <span class="fw-bold text-success">{{ $laporanSudah }} / {{ $total }} Berkas</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ARAI CHATBOT WIDGET --}}
    <div id="arai-chat-widget">
        <button id="arai-btn"><i class="fas fa-robot"></i></button>
        <div id="arai-chat-window">
            <div class="arai-header" id="arai-drag-handle">
                <div><i class="fas fa-robot me-2"></i> Tanya Arai (Admin)</div>
                <button id="close-arai" style="background:transparent; border:none; color:white; font-size:1.5rem;">&times;</button>
            </div>
            <div class="arai-body" id="arai-body">
                <div class="chat-bubble chat-ai">Halo Admin! 👋 Mau info data apa hari ini?</div>
            </div>
            <div class="arai-footer">
                <div class="arai-input-group">
                    <input type="text" id="arai-input" class="arai-input" placeholder="Ketik pertanyaan...">
                    <button id="arai-send" class="arai-send"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // 1. WAKTU REAL-TIME
        function updateTime() {
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            document.getElementById('currentTime').textContent = new Date().toLocaleDateString('id-ID', options);
        }
        setInterval(updateTime, 1000); updateTime();

        // 2. GRAFIK LAPGAS (Donut)
        new Chart(document.getElementById('lapgasChart'), {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Pending'],
                datasets: [{
                    data: [{{ $laporanSudah ?? 0 }}, {{ $laporanBelum ?? 0 }}],
                    backgroundColor: ['#10b981', '#f72585'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                cutout: '70%'
            }
        });

        // 3. GRAFIK BERKAS DTS (Bar)
        new Chart(document.getElementById('dtsBerkasChart'), {
            type: 'bar',
            data: {
                labels: ['Lengkap', 'Belum Lengkap'],
                datasets: [{
                    label: 'Jumlah Kegiatan',
                    data: [{{ ($totalKegiatan ?? 0) - ($dtsIncomplete ?? 0) }}, {{ $dtsIncomplete ?? 0 }}],
                    backgroundColor: ['#4361ee', '#f8961e'],
                    borderRadius: 8,
                    barThickness: 50
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { display: false } },
                    x: { grid: { display: false } }
                },
                plugins: { legend: { display: false } }
            }
        });

        // 4. ARAI CHATBOT LOGIC
        $(document).ready(function() {
            let isDragging = false;
            $('#arai-btn').draggable({
                containment: 'window',
                start: function() { isDragging = true; },
                stop: function() { setTimeout(() => isDragging = false, 50); }
            });

            $('#arai-btn').click(function() {
                if(!isDragging) $('#arai-chat-window').toggleClass('active');
            });

            $('#close-arai').click(function() { $('#arai-chat-window').removeClass('active'); });

            function sendMessage() {
                let msg = $('#arai-input').val();
                if(!msg) return;
                $('#arai-body').append('<div class="chat-bubble chat-user">' + msg + '</div>');
                $('#arai-input').val('');

                let tid = 'typing-' + Date.now();
                $('#arai-body').append('<div id="'+tid+'" class="chat-bubble chat-ai">Arai sedang memproses...</div>');
                $('#arai-body').scrollTop($('#arai-body')[0].scrollHeight);

                $.post('{{ route("chat.arai") }}', { _token: '{{ csrf_token() }}', message: msg }, function(res) {
                    $('#'+tid).remove();
                    $('#arai-body').append('<div class="chat-bubble chat-ai">' + res.reply + '</div>');
                    $('#arai-body').scrollTop($('#arai-body')[0].scrollHeight);
                });
            }

            $('#arai-send').click(sendMessage);
            $('#arai-input').keypress(function(e) { if(e.which == 13) sendMessage(); });
        });
    </script>
</body>
</html>
