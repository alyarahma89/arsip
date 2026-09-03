<!DOCTYPE html>
<html lang="id">
<head>
    @include('dashboard.partials.head_pegawai', ['title' => 'Edit Profil'])
</head>
<body>
    <div class="wrapper">
        {{-- SIDEBAR --}}
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-icon"><i class="fas fa-layer-group"></i></div>
                <div class="brand-text"><h5>Portal DTS</h5><span>Pegawai Dashboard</span></div>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
                <li class="nav-item"><a href="{{ route('pegawai.dts') }}" class="nav-link"><i class="fas fa-calendar-check"></i> <span>Kegiatan DTS</span></a></li>
                <li class="nav-item"><a href="{{ route('pegawai.lapgas') }}" class="nav-link"><i class="fas fa-file-signature"></i> <span>Laporan Tugas</span></a></li>
                <li class="nav-item"><a href="{{ route('pegawai.export_rekap') }}" class="nav-link"><i class="fas fa-file-export"></i> <span>Export Rekap</span></a></li>
            </ul>
            <div class="upgrade-box">
                <div class="upgrade-icon"><i class="fas fa-power-off fa-lg"></i></div>
                <h6 class="fw-bold mb-1">Selesai Bekerja?</h6>
                <p class="small opacity-75 mb-3" style="font-size: 0.7rem;">Jangan lupa logout demi keamanan.</p>
                <form action="{{ route('logout') }}" method="POST"> @csrf <button class="btn btn-light btn-sm w-100 rounded-pill fw-bold text-primary">Logout Sekarang</button> </form>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="main-content">
            {{-- Top Bar Sederhana --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div><h2 class="fw-bold mb-1">Pengaturan Akun</h2><p class="text-muted">Perbarui informasi profil Anda di sini.</p></div>
                <a href="{{ route('dashboard') }}" class="btn btn-light border rounded-pill"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    {{-- Alert Sukses --}}
                    @if(session('success'))
                        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                            <i class="fas fa-check-circle me-2 fs-4"></i> {{ session('success') }}
                        </div>
                    @endif

                    <div class="stat-card">
                        <form action="{{ route('profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="text-center mb-5">
                                <div class="mx-auto bg-primary text-white d-flex align-items-center justify-content-center rounded-circle shadow" style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <h5 class="mt-3 fw-bold">{{ $user->name }}</h5>
                                <span class="badge bg-light text-primary border rounded-pill">{{ ucfirst($user->role) }}</span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control rounded-pill bg-light border-0 px-3" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">NIP</label>
                                    <input type="text" name="nip" class="form-control rounded-pill bg-light border-0 px-3" value="{{ old('nip', $user->nip) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Jabatan</label>
                                    <input type="text" name="jabatan" class="form-control rounded-pill bg-light border-0 px-3" value="{{ old('jabatan', $user->jabatan) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-muted">Email</label>
                                    <input type="email" name="email" class="form-control rounded-pill bg-light border-0 px-3" value="{{ old('email', $user->email) }}" required>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="p-3 bg-warning bg-opacity-10 rounded-3 border border-warning border-opacity-25">
                                        <h6 class="fw-bold text-warning mb-2"><i class="fas fa-lock me-1"></i> Ganti Password</h6>
                                        <p class="small text-muted mb-3">Kosongkan jika tidak ingin mengganti password.</p>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <input type="password" name="password" class="form-control rounded-pill border-0 shadow-sm px-3" placeholder="Password Baru">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="password" name="password_confirmation" class="form-control rounded-pill border-0 shadow-sm px-3" placeholder="Konfirmasi Password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">
                                    <i class="fas fa-save me-1"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
