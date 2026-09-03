<!DOCTYPE html>
<html lang="id">
<head>
    @include('dashboard.partials.head_pegawai', ['title' => 'Laporan Tugas'])
</head>
<body>
    <div class="wrapper">

        {{-- SIDEBAR --}}
        <aside class="sidebar">
            {{-- 1. LOGO KOMDIGI (VERSI TENGAH & BAWAH) - MENGIKUTI DASHBOARD --}}
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
                    <a href="{{ route('dashboard') }}" class="nav-link">
                        <i class="fas fa-th-large"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pegawai.dts') }}" class="nav-link">
                        <i class="fas fa-calendar-check"></i> <span>Kegiatan DTS</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('pegawai.lapgas') }}" class="nav-link active">
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
                <div><h2 class="fw-bold mb-1">Laporan Tugas</h2><p class="text-muted mb-0">Pantau dan upload laporan tugas Anda.</p></div>
                <div class="d-flex align-items-center gap-3">

                    <form action="{{ route('pegawai.lapgas') }}" method="GET" class="search-box d-none d-md-flex">
                        <button type="submit" class="btn p-0 border-0 text-muted"><i class="fas fa-search"></i></button>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tugas...">
                    </form>

                    {{-- NOTIFIKASI --}}
                    <div class="dropdown">
                        <button class="notif-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="far fa-bell"></i>
                            @if(isset($pending) && $pending > 0)
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.6rem; padding: 4px 6px;">
                                    {{ $pending }}
                                </span>
                            @endif
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 mt-3" style="width: 320px; border-radius: 12px; max-height: 400px; overflow-y: auto;">
                            <li class="p-3 bg-primary text-white d-flex justify-content-between align-items-center sticky-top">
                                <h6 class="mb-0 fw-bold"><i class="far fa-bell me-2"></i>Notifikasi</h6>
                                @if(isset($pending) && $pending > 0) <span class="badge bg-white text-primary rounded-pill">{{ $pending }} Baru</span> @endif
                            </li>
                            @if(isset($pending) && $pending > 0)
                                @foreach($myAssignments as $task)
                                    @if(!$task->status_laporan)
                                    <li>
                                        <a class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3" href="{{ route('pegawai.lapgas', ['search' => $task->kegiatan]) }}" style="white-space: normal;">
                                            <div class="bg-warning bg-opacity-10 p-2 rounded-circle text-warning flex-shrink-0" style="height: 40px; width: 40px; display:flex; align-items:center; justify-content:center;">
                                                <i class="fas fa-exclamation-circle"></i>
                                            </div>
                                            <div class="w-100">
                                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.85rem; line-height: 1.4;">{{ Str::limit($task->kegiatan, 50) }}</h6>
                                                <div class="d-flex justify-content-between align-items-center mt-1">
                                                    <small class="text-muted" style="font-size: 0.75rem;"><i class="far fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($task->tanggal)->format('d M Y') }}</small>
                                                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Segera Kerjakan</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    @endif
                                @endforeach
                            @else
                                <li class="p-5 text-center">
                                    <div class="mb-3 text-success bg-success bg-opacity-10 p-3 rounded-circle d-inline-block"><i class="fas fa-check-circle fa-2x"></i></div>
                                    <h6 class="fw-bold text-dark">Kerja Bagus!</h6>
                                    <p class="text-muted small mb-0">Semua laporan tugas sudah selesai.</p>
                                </li>
                            @endif
                        </ul>
                    </div>

                    {{-- PROFILE DROPDOWN --}}
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
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="fas fa-user-circle me-2 text-primary"></i> Edit Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button class="dropdown-item py-2 text-danger fw-bold" type="submit"><i class="fas fa-sign-out-alt me-2"></i> Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- CONTENT TABLE --}}
            <div class="table-card mt-4">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th width="5%">No</th><th width="35%">Kegiatan</th><th width="15%">Tanggal</th><th width="15%">Status</th><th width="15%" class="text-center">Aksi</th></tr></thead>
                        <tbody>
                            @forelse($myAssignments as $index => $assign)
                            @php
                                $isSelesai = $assign->status_laporan == 1;
                                $isOverdue = !$isSelesai && \Carbon\Carbon::parse($assign->tanggal)->diffInDays(now(), false) > -2;
                            @endphp
                            <tr style="{{ $isOverdue ? 'background-color: #fef2f2;' : '' }}">
                                <td>{{ $myAssignments->firstItem() + $index }}</td>
                                <td>{{ $assign->kegiatan }}</td>
                                <td class="text-muted small">{{ \Carbon\Carbon::parse($assign->tanggal)->isoFormat('D MMM Y') }}</td>
                                <td>
                                    @if($isSelesai) <span class="status-badge bg-success bg-opacity-10 text-success"><i class="fas fa-check-circle"></i> Selesai</span>
                                    @elseif($isOverdue) <span class="status-badge bg-danger bg-opacity-10 text-danger"><i class="fas fa-exclamation-circle"></i> Late</span>
                                    @else <span class="status-badge bg-warning bg-opacity-10 text-warning"><i class="fas fa-clock"></i> Pending</span> @endif
                                </td>

                                {{-- KOLOM AKSI (HANYA TOMBOL FOLDER) --}}
                                <td>
                                    <div class="d-flex flex-column gap-2">
                                        @if($assign->link_bukti && $assign->link_bukti != '-')
                                            @php
                                                $link = $assign->link_bukti;
                                                $href = str_contains($link, 'http') ? $link : 'https://drive.google.com/drive/search?q='.urlencode($link);
                                            @endphp
                                            <a href="{{ $href }}" target="_blank" class="btn btn-sm {{ $isSelesai ? 'btn-outline-success' : 'btn-primary' }} rounded-pill w-100" style="font-size: 0.75rem;">
                                                <i class="{{ $isSelesai ? 'fas fa-eye' : 'fas fa-upload' }} me-1"></i> {{ $isSelesai ? 'Bukti' : 'Upload' }}
                                            </a>
                                        @else
                                            <button class="btn btn-light border btn-sm rounded-pill w-100 text-muted" disabled style="font-size: 0.75rem;"><i class="fas fa-hourglass-half me-1"></i> Wait Link</button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty <tr><td colspan="5" class="text-center text-muted py-4">Belum ada tugas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    {{ $myAssignments->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ========================================== --}}
    {{-- WIDGET CHATBOT ARAI (WARNA BIRU KOMDIGI) --}}
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

        /* Chat AI: Biru muda */
        .chat-ai { background: #e0e7ff; color: #1e3a8a; align-self: flex-start; border-bottom-left-radius: 0; }

        /* Chat User: Biru Komdigi */
        .chat-user { background: #2D3194; color: white; align-self: flex-end; border-bottom-right-radius: 0; }

        /* Footer Chat */
        .arai-footer { padding: 15px; background: white; border-top: 1px solid #e2e8f0; }
        .arai-input-group { display: flex; gap: 10px; }
        .arai-input { flex-grow: 1; border: 1px solid #cbd5e1; border-radius: 20px; padding: 8px 15px; outline: none; }

        /* Tombol Kirim - BIRU */
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
                    Halo Kak! 👋 Aku Arai. Ada laporan tugas yang mau ditanyain hari ini?
                </div>
            </div>
            <div class="arai-footer">
                <div class="arai-input-group">
                    <input type="text" id="arai-input" class="arai-input" placeholder="Tanya Arai...">
                    <button id="arai-send" class="arai-send"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>

        <button id="arai-btn"><i class="fas fa-robot"></i></button>
    </div>

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
                $('#arai-body').scrollTop($('#arai-body')[0].scrollHeight);

                // Tembak ke Backend (Groq Llama 3)
                $.ajax({
                    url: '{{ route("chat.arai") }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}', message: msg },
                    success: function(response) {
                        $('#' + typingId).remove();
                        // Ubah \n jadi <br> dan *bold* jadi <b>
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
