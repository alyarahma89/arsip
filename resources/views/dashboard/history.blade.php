<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Upload - Komdigi</title>
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/komdigi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --komdigi-blue-dark: #0E2C6C;
            --komdigi-blue-light: #00AEEF;
            --komdigi-bg: #F4F7F9;
        }
        body { background-color: var(--komdigi-bg); font-family: 'Segoe UI', sans-serif; color: #334155; }

        .header-section {
            background: linear-gradient(135deg, var(--komdigi-blue-dark) 0%, #1a4299 100%);
            color: white; border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(14, 44, 108, 0.2); position: relative; overflow: hidden;
        }
        .header-section::after {
            content: ''; position: absolute; top: 0; right: 0; width: 150px; height: 100%;
            background: linear-gradient(to bottom left, var(--komdigi-blue-light), transparent);
            opacity: 0.2; pointer-events: none;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.15); color: white; border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px); transition: 0.3s;
        }
        .btn-back:hover { background: white; color: var(--komdigi-blue-dark); }

        .card-history {
            background: white; border-radius: 12px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); overflow: hidden;
        }

        .table thead th {
            background: #f8fafc; color: var(--komdigi-blue-dark); font-weight: 700;
            text-transform: uppercase; font-size: 0.85rem; padding: 1rem; border-bottom: 2px solid #e2e8f0;
        }
        .table tbody td { padding: 1rem; vertical-align: middle; }

        .icon-type {
            width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;
            border-radius: 8px; font-size: 1rem; margin-right: 10px;
        }
        .icon-dts { background-color: #e0e7ff; color: #4361ee; }
        .icon-lapgas { background-color: #d1fae5; color: #10b981; }

        .badge-sukses { background-color: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
        .badge-gagal { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }

        .pagination .page-link { color: var(--komdigi-blue-dark); }
        .pagination .page-item.active .page-link { background-color: var(--komdigi-blue-dark); border-color: var(--komdigi-blue-dark); }
    </style>
</head>
<body class="p-4">
    <div class="container" style="max-width: 1200px;">

        <div class="header-section d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-history me-2"></i> Riwayat Upload</h2>
                <p class="mb-0 opacity-75">Log aktivitas import data ke dalam sistem</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-back btn-sm px-3 py-2 rounded-pill shadow-sm text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

        <div class="card-history">
            <div class="table-responsive">
                <table class="table mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4">File / Tipe</th>
                            <th>Status</th>
                            <th>Pesan Sistem</th>
                            <th>User</th>
                            <th>Waktu Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $history)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="icon-type {{ $history->type == 'DTS' ? 'icon-dts' : 'icon-lapgas' }}">
                                        <i class="fas {{ $history->type == 'DTS' ? 'fa-calendar-alt' : 'fa-tasks' }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $history->file_name }}</div>
                                        <small class="text-muted text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">{{ $history->type }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($history->status == 'Sukses')
                                    <span class="badge badge-sukses rounded-pill px-3 py-2"><i class="fas fa-check-circle me-1"></i> Sukses</span>
                                @else
                                    <span class="badge badge-gagal rounded-pill px-3 py-2"><i class="fas fa-times-circle me-1"></i> Gagal</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($history->message, 50) }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle text-secondary d-flex align-items-center justify-content-center me-2" style="width: 28px; height: 28px; font-size: 0.8rem;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="small fw-medium">{{ $history->user_name }}</span>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted fw-medium">
                                    <i class="far fa-clock me-1"></i> {{ $history->created_at->diffForHumans() }}
                                </small>
                                <div class="small text-muted" style="font-size: 0.7rem;">
                                    {{ $history->created_at->format('d M Y, H:i') }}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" width="60" class="opacity-25 mb-3">
                                <p class="text-muted mb-0">Belum ada riwayat upload.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($histories->hasPages())
            <div class="p-3 border-top">
                {{ $histories->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>

    </div>
</body>
</html>
