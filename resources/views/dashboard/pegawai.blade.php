<!DOCTYPE html>
<html lang="id">
<head>
    @include('dashboard.partials.head_pegawai', ['title' => 'Dashboard Pegawai'])
</head>
<body>
    <div class="wrapper">
        {{-- SIDEBAR --}}
        <aside class="sidebar">
            {{-- 1. LOGO KOMDIGI (VERSI TENGAH & BAWAH) --}}
            <div class="sidebar-logo d-flex flex-column align-items-center text-center mb-4 pb-3 border-bottom mx-3 mt-5">
                <img src="{{ asset('image/komdigi.png') }}" alt="Logo Komdigi" style="width: 70px; height: auto; margin-bottom: 15px; object-fit: contain;">
                <div style="line-height: 1.2;">
                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 1.1rem; background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Sistem Arsip</h6>
                    <small class="text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px;">BBPSDMP Komdigi</small>
                </div>
            </div>

            {{-- 2. MENU NAVIGASI PEGAWAI --}}
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link active">
                        <i class="fas fa-th-large"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pegawai.dts') }}" class="nav-link">
                        <i class="fas fa-calendar-check"></i> <span>Kegiatan DTS</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pegawai.lapgas') }}" class="nav-link">
                        <i class="fas fa-file-signature"></i> <span>Laporan Tugas</span>
                    </a>
                </li>
            </ul>

            {{-- 3. FOOTER LOGOUT (DENGAN GARIS PEMBATAS) --}}
            <div class="mt-auto pt-4 border-top mx-3 mb-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="btn btn-danger w-100 rounded-pill fw-bold" type="submit">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
                <div class="text-center mt-3">
                    <p class="text-muted small mb-0" style="font-size: 0.7rem;">© 2026 BBPSDMP</p>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="main-content">
            {{-- TOP BAR --}}
            <div class="top-bar">
                <div><h2 class="fw-bold mb-1">Hi, {{ Auth::user()->name }}! 👋</h2><p class="text-muted mb-0">Ringkasan statistik kinerja Anda.</p></div>
                <div class="d-flex align-items-center gap-3">

                    {{-- 1. NOTIFIKASI BUTTON (INTERAKTIF) --}}
                    <div class="dropdown">
                        <button class="notif-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-bell"></i>
                            @if($pending > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.6rem; padding: 4px 6px;">
                                    {{ $pending }}
                                </span>
                            @endif
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 mt-3" style="width: 320px; border-radius: 12px; overflow: hidden; max-height: 400px; overflow-y: auto;">
                            {{-- Header --}}
                            <li class="p-3 bg-primary text-white d-flex justify-content-between align-items-center sticky-top">
                                <h6 class="mb-0 fw-bold"><i class="far fa-bell me-2"></i>Notifikasi</h6>
                                @if($pending > 0) <span class="badge bg-white text-primary rounded-pill">{{ $pending }} Baru</span> @endif
                            </li>

                            {{-- List Notif --}}
                            @if($pending > 0 && isset($notifTasks) && count($notifTasks) > 0)
                                @foreach($notifTasks as $task)
                                <li>
                                    <a class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3"
                                       href="{{ route('pegawai.lapgas', ['search' => $task->kegiatan]) }}"
                                       style="white-space: normal;">
                                        <div class="bg-warning bg-opacity-10 p-2 rounded-circle text-warning flex-shrink-0" style="height: 40px; width: 40px; display:flex; align-items:center; justify-content:center;">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </div>
                                        <div class="w-100">
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.85rem; line-height: 1.4;">{{ Str::limit($task->kegiatan, 50) }}</h6>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    <i class="far fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($task->tanggal)->format('d M Y') }}
                                                </small>
                                                <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Segera Kerjakan</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                                @endforeach
                                <li>
                                    <a class="dropdown-item p-3 text-center text-primary fw-bold small bg-light" href="{{ route('pegawai.lapgas') }}">
                                        Lihat Semua {{ $pending }} Tugas Pending <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </li>
                            @else
                                <li class="p-5 text-center">
                                    <div class="mb-3 text-success bg-success bg-opacity-10 p-3 rounded-circle d-inline-block">
                                        <i class="fas fa-check-circle fa-2x"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark">Kerja Bagus!</h6>
                                    <p class="text-muted small mb-0">Semua laporan tugas sudah selesai.</p>
                                </li>
                            @endif
                        </ul>
                    </div>

                    {{-- 2. PROFILE DROPDOWN (SUDAH DIPERBAIKI) --}}
                    <div class="dropdown">
                        <div class="user-profile cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">
                            <div class="profile-info d-none d-md-block text-end">
                                <h6 class="mb-0 text-dark">{{ Auth::user()->name }}</h6>
                                <small class="text-muted">Pegawai</small>
                            </div>
                            <div class="profile-img ms-2">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3" style="border-radius: 12px; width: 200px;">
                            <li><h6 class="dropdown-header text-uppercase small fw-bold">Akun Saya</h6></li>

                            <li>
                                <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-circle me-2 text-primary"></i> Edit Profil
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item py-2 text-danger fw-bold" type="submit">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- 3. METRIC CARDS --}}
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon-box bg-icon-primary"><i class="fas fa-calendar-check"></i></div>
                        <p class="text-muted small mb-1 fw-bold">Total Kegiatan DTS</p>
                        <h2 class="fw-bold mb-0">{{ $totalDTS }}</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon-box bg-icon-success"><i class="fas fa-file-signature"></i></div>
                        <p class="text-muted small mb-1 fw-bold">Total Lapgas</p>
                        <h2 class="fw-bold mb-0">{{ $totalLapgas }}</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="icon-box bg-icon-warning"><i class="fas fa-clock"></i></div>
                        <p class="text-muted small mb-1 fw-bold">Lapgas Pending</p>
                        <h2 class="fw-bold mb-0">{{ $pending }}</h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card p-3 d-flex align-items-center">
                        <div style="width: 80px; height: 80px; margin-right: 15px;"><canvas id="rasioPendingChart"></canvas></div>
                        <div><h6 class="fw-bold mb-1">Status</h6><small class="text-muted">Laporan</small></div>
                    </div>
                </div>
            </div>

            {{-- 4. GRAFIK ANALISIS --}}
            <div class="row g-4">
                {{-- Grafik Donut --}}
                <div class="col-md-4">
                    <div class="stat-card" style="min-height: 400px;">
                        <h5 class="fw-bold mb-4">Proporsi Beban Kerja</h5>
                        <div style="height: 250px; position: relative; margin-top: 20px;">
                            <canvas id="proporsiChart"></canvas>
                        </div>
                        <div class="mt-4 text-center">
                            <span class="badge bg-primary me-2">DTS: {{ $totalDTS }}</span>
                            <span class="badge bg-success">Lapgas: {{ $totalLapgas }}</span>
                        </div>
                    </div>
                </div>

                {{-- Grafik Batang --}}
                <div class="col-md-8">
                    <div class="stat-card" style="min-height: 400px;">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold">Tren Aktivitas Bulanan</h5>
                            {{-- Dropdown Filter Tahun --}}
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm border dropdown-toggle fw-bold text-muted" type="button" data-bs-toggle="dropdown">
                                    <i class="far fa-calendar-alt me-1"></i> Tahun: {{ $filterTahun }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm">
                                    @foreach($availableYears as $year)
                                    <li><a class="dropdown-item {{ $filterTahun == $year ? 'active bg-primary' : '' }}" href="{{ route('dashboard', ['tahun' => $year]) }}">{{ $year }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div style="height: 300px;">
                            <canvas id="trenChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- CHART SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 1. Grafik Mini
        new Chart(document.getElementById('rasioPendingChart'), {
            type: 'doughnut',
            data: { labels: ['Selesai', 'Pending'], datasets: [{ data: [{{ $selesai }}, {{ $pending }}], backgroundColor: ['#10b981', '#fbbf24'], borderWidth: 0 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '70%' }
        });

        // 2. Grafik Proporsi
        new Chart(document.getElementById('proporsiChart'), {
            type: 'doughnut',
            data: {
                labels: ['Kegiatan DTS', 'Laporan Tugas'],
                datasets: [{ data: [{{ $totalDTS }}, {{ $totalLapgas }}], backgroundColor: ['#4361ee', '#4cc9f0'], borderWidth: 2, borderColor: '#ffffff' }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } },
                cutout: '60%'
            }
        });

        // 3. Grafik Tren Bulanan
        new Chart(document.getElementById('trenChart'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [
                    { label: 'Kegiatan DTS', data: @json(array_values($dtsBulanan)), backgroundColor: '#4361ee', borderRadius: 5 },
                    { label: 'Laporan Tugas', data: @json(array_values($lapgasBulanan)), backgroundColor: '#4cc9f0', borderRadius: 5 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } },
                plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true } } }
            }
        });
    </script>

    {{-- ========================================== --}}
    {{-- WIDGET CHATBOT ARAI (WARNA BIRU KOMDIGI - NO DRAG) --}}
    {{-- ========================================== --}}
    <style>
        /* Desain Tombol Melayang Arai - BIRU */
        #arai-btn {
            position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px;
            background: linear-gradient(135deg, #2D3194, #4338ca); color: white;
            border-radius: 50%; border: none; box-shadow: 0 10px 25px rgba(45, 49, 148, 0.4);
            font-size: 28px; cursor: pointer; z-index: 9999; transition: transform 0.3s;
            display: flex; justify-content: center; align-items: center;
        }
        #arai-btn:hover { transform: scale(1.1); }

        /* Desain Jendela Chat Arai */
        #arai-chat-window {
            position: fixed; bottom: 100px; right: 30px; width: 350px; height: 450px;
            background: white; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            display: flex; flex-direction: column; z-index: 9999; overflow: hidden;
            transform: scale(0); transform-origin: bottom right; transition: transform 0.3s ease;
        }
        #arai-chat-window.active { transform: scale(1); }

        /* Header Chat - BIRU */
        .arai-header {
            background: linear-gradient(135deg, #2D3194, #4338ca); color: white;
            padding: 15px 20px; font-weight: bold; display: flex; justify-content: space-between;
            align-items: center;
        }

        /* Body Chat */
        .arai-body {
            flex-grow: 1; padding: 15px; overflow-y: auto; background-color: #f8fafc;
            display: flex; flex-direction: column; gap: 10px;
        }
        .chat-bubble { max-width: 80%; padding: 10px 15px; border-radius: 15px; font-size: 0.9rem; line-height: 1.4; }

        /* Chat AI: Warna background biru sangat muda, text biru tua */
        .chat-ai { background: #e0e7ff; color: #1e3a8a; align-self: flex-start; border-bottom-left-radius: 0; }

        /* Chat User: Warna background biru tua Komdigi, text putih */
        .chat-user { background: #2D3194; color: white; align-self: flex-end; border-bottom-right-radius: 0; }

        /* Footer Chat */
        .arai-footer { padding: 15px; background: white; border-top: 1px solid #e2e8f0; }
        .arai-input-group { display: flex; gap: 10px; }
        .arai-input { flex-grow: 1; border: 1px solid #cbd5e1; border-radius: 20px; padding: 8px 15px; outline: none; }

        /* Tombol Send - BIRU */
        .arai-send { background: #2D3194; color: white; border: none; border-radius: 50%; width: 40px; height: 40px; cursor: pointer; transition: 0.2s; }
        .arai-send:hover { background: #4338ca; }
    </style>

    <div id="arai-chat-widget">
        <div id="arai-chat-window">
            <div class="arai-header">
                <div><i class="fas fa-robot me-2"></i> Tanya Arai (AI)</div>
                <button id="close-arai" style="background: transparent; border: none; color: white; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            <div class="arai-body" id="arai-body">
                <div class="chat-bubble chat-ai">
                    Halo Kak! 👋 Aku Arai. Ada yang mau ditanyain soal total kegiatan atau tugas Kakak hari ini?
                </div>
            </div>
            <div class="arai-footer">
                <div class="arai-input-group">
                    <input type="text" id="arai-input" class="arai-input" placeholder="Tanya sesuatu ke Arai...">
                    <button id="arai-send" class="arai-send"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>

        <button id="arai-btn"><i class="fas fa-robot"></i></button>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            // Buka/Tutup Chat Window
            $('#arai-btn').click(function() { $('#arai-chat-window').toggleClass('active'); });
            $('#close-arai').click(function() { $('#arai-chat-window').removeClass('active'); });

            // Fungsi Kirim Pesan
            function sendAraiMessage() {
                let msg = $('#arai-input').val().trim();
                if (msg === '') return;

                // Tampilkan pesan User
                $('#arai-body').append('<div class="chat-bubble chat-user">' + msg + '</div>');
                $('#arai-input').val('');

                // Tampilkan animasi "Arai sedang mengetik..."
                let typingId = 'typing-' + Date.now();
                $('#arai-body').append('<div id="' + typingId + '" class="chat-bubble chat-ai"><i class="fas fa-ellipsis-h fa-beat"></i> Arai lagi mikir...</div>');

                // Scroll ke bawah
                $('#arai-body').scrollTop($('#arai-body')[0].scrollHeight);

                // Tembak ke Backend (Groq Llama 3)
                $.ajax({
                    url: '{{ route("chat.arai") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', message: msg },
                    success: function(response) {
                        $('#' + typingId).remove();
                        let formattedReply = response.reply.replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\*/g, '<b>$1</b>');
                        $('#arai-body').append('<div class="chat-bubble chat-ai">' + formattedReply + '</div>');
                        $('#arai-body').scrollTop($('#arai-body')[0].scrollHeight);
                    },
                    error: function() {
                        $('#' + typingId).remove();
                        $('#arai-body').append('<div class="chat-bubble chat-ai text-danger">Aduh, sinyal Arai lagi jelek nih Kak. Coba lagi ya! 😭</div>');
                    }
                });
            }

            // Klik tombol kirim
            $('#arai-send').click(sendAraiMessage);

            // Tekan Enter di keyboard
            $('#arai-input').keypress(function(e) {
                if(e.which == 13) { sendAraiMessage(); }
            });
        });
    </script>
</body>
</html>
