<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow p-4" style="width: 400px;">
        <h4 class="text-center mb-3">Masukkan Kode OTP</h4>
        <p class="text-center text-muted small">Kode telah dikirim ke: <strong>{{ request('email') }}</strong></p>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('otp.check') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value="{{ request('email') }}">

            <input type="hidden" name="type" value="{{ request('type') }}">

            <div class="mb-3">
                <label>Kode 6 Digit</label>
                <input type="number" name="otp" class="form-control text-center" placeholder="123456" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Verifikasi</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="text-decoration-none">Ganti Email</a>
        </div>
    </div>

</body>
</html>
