<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login Arsip Negara</title>
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/komdigi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            width: 100%;
            max-width: 400px; /* Lebar maksimal agar tidak terlalu lebar */
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 2.5rem; /* Padding yang pas */
            background: white;
        }
        .logo-img {
            width: 60px; /* Ukuran logo diperkecil sedikit agar pas */
            margin-bottom: 15px;
        }
        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            border-color: #4361ee;
        }
        .btn-primary {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        .input-group-text {
            border-radius: 10px 0 0 10px;
            border-color: #e2e8f0;
            background-color: #f8fafc;
        }
        .captcha-img img {
            border-radius: 8px;
            height: 38px; /* Tinggi captcha disamakan dengan input */
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

    <div class="login-card">
        <div class="text-center mb-4">
            {{-- LOGO --}}
            <img src="{{ asset('image/komdigi.png') }}" alt="Logo Komdigi" class="logo-img">

            <h4 class="fw-bold text-dark mb-1">Sistem Arsip</h4>
            <h6 class="text-muted fw-normal" style="font-size: 0.9rem;">BBPSDM Komdigi</h6>
        </div>

        {{-- ALERT ERROR --}}
        {{-- AREA NOTIFIKASI ERROR (KOMPLIT) --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show border-0 bg-danger bg-opacity-10 text-danger rounded-3 mb-4" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-circle mt-1 me-2 flex-shrink-0"></i>
                    <div>
                        <strong>Gagal Login!</strong>
                        <ul class="mb-0 ps-3 mt-1 small">
                            {{-- Loop semua error yang dikirim dari controller --}}
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{-- BATAS AREA NOTIFIKASI --}}

        {{-- ALERT SUKSES LOGOUT --}}
        @if(session('success'))
            <div class="alert alert-success py-2 small d-flex align-items-center mb-3 border-0 bg-success bg-opacity-10 text-success rounded-3">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Email Dinas</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="nama@arsip.go.id" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Verifikasi Keamanan</label>
                <div class="row g-2 align-items-center">
                    <div class="col-6">
                        <div class="bg-light border rounded px-3 py-2 fw-bold text-center shadow-sm" style="font-size: 1.2rem;">
                            {{-- Ini yang tadi error, sekarang pasti aman karena sudah dikirim dari controller --}}
                            {{ $n1 }} + {{ $n2 }} = ?
                        </div>
                        {{-- Input hidden untuk menyimpan kunci jawaban terenkripsi --}}
                        <input type="hidden" name="captcha_hash" value="{{ $hash }}">
                    </div>
                    <div class="col-6">
                        <input type="number" name="captcha" class="form-control h-100 py-2" placeholder="Hasil" required style="text-align: center; font-weight: bold; border: 2px solid #e2e8f0;">
                    </div>
                </div>
                @error('captcha')
                    <small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>

        <div class="text-center mt-4 pt-3 border-top">
            <small class="text-muted">Pegawai baru? <a href="{{ route('register') }}" class="text-decoration-none fw-bold text-primary">Daftar disini</a></small>
        </div>
    </div>

    <script type="text/javascript">
        $('#reload-captcha').click(function () {
            // Animasi loading icon
            $(this).find('i').addClass('fa-spin');

            $.ajax({
                type: 'GET',
                url: '{{ route("captcha.reload") }}',
                success: function (data) {
                    $(".captcha-img").html(data.captcha);
                    $('#reload-captcha').find('i').removeClass('fa-spin'); // Stop animasi
                }
            });
        });
    </script>

</body>
</html>
