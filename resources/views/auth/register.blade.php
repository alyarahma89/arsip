<!DOCTYPE html>
<html lang="id">
<head>
    <title>Register Arsip Negara</title>
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/komdigi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', sans-serif;
        }

        .register-card {
            width: 100%;
            max-width: 500px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 2.5rem;
            background: white;
        }

        .logo-img {
            width: 60px;
            margin-bottom: 15px;
        }

        .form-control {
            border-radius: 10px;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(67,97,238,0.15);
            border-color: #4361ee;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67,97,238,0.3);
        }

        .input-group-text {
            border-radius: 10px 0 0 10px;
            border-color: #e2e8f0;
            background-color: #f8fafc;
        }

        .required {
            color: #dc3545;
        }
    </style>
</head>

<body class="d-flex align-items-center justify-content-center min-vh-100 p-3">

<div class="register-card">

    <!-- HEADER -->
    <div class="text-center mb-4">
        <img src="{{ asset('image/komdigi.png') }}" class="logo-img">
        <h4 class="fw-bold text-dark mb-1">Register Pegawai</h4>
        <h6 class="text-muted fw-normal" style="font-size:0.9rem;">BBPSDMP Komdigi</h6>
    </div>

    <!-- ERROR -->
    @if($errors->any())
        <div class="alert alert-danger py-2 small rounded-3">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register.submit') }}" method="POST">
        @csrf

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label small fw-bold text-muted">Nama Depan <span class="required">*</span></label>
                <input type="text" name="first_name" class="form-control" placeholder="Budi" required>
            </div>
            <div class="col-6">
                <label class="form-label small fw-bold text-muted">Nama Belakang <span class="required">*</span></label>
                <input type="text" name="last_name" class="form-control" placeholder="Santoso" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Kode Registrasi Unit (TT Code) <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-key text-muted"></i></span>
                <input type="number" name="registration_code" class="form-control" placeholder="Kode Unit" required>
            </div>
            <small class="text-muted" style="font-size:0.75rem;">
                <i class="fas fa-info-circle"></i> Minta kode ini ke atasan Anda
            </small>
        </div>

        <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Email Dinas <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                <input type="email" name="email" class="form-control" placeholder="nama@arsip.go.id" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label small fw-bold text-muted">Nomor WhatsApp / HP <span class="required">*</span></label>
            <div class="input-group">
                <span class="input-group-text">+62</span>
                <input type="number" name="phone" class="form-control" placeholder="812xxxxxxx" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 shadow-sm">
            DAFTAR AKUN <i class="fas fa-user-plus ms-2"></i>
        </button>
    </form>

    <div class="text-center mt-4 pt-3 border-top">
        <small class="text-muted">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-primary">Login disini</a>
        </small>
    </div>

</div>


</body>
</html>

