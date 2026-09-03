<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lapgas Manual - Komdigi</title>
    <link rel="icon" type="image/png" href="{{ asset('image/komdigi.png') }}">
    <link rel="shortcut icon" href="{{ asset('image/komdigi.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --komdigi-blue-dark: #0E2C6C;
            --komdigi-blue-light: #00AEEF;
            --komdigi-accent: #FDB913;
            --komdigi-bg: #F4F7F9;
        }
        body { background-color: var(--komdigi-bg); font-family: 'Segoe UI', sans-serif; color: #334155; }

        /* Header Style */
        .header-section {
            background: linear-gradient(135deg, var(--komdigi-blue-dark) 0%, #1a4299 100%);
            color: white; border-radius: 12px; padding: 2rem; margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(14, 44, 108, 0.2); position: relative; overflow: hidden;
        }
        .header-section::after {
            content: ''; position: absolute; top: 0; right: 0; width: 200px; height: 100%;
            background: linear-gradient(to bottom left, var(--komdigi-blue-light), transparent);
            opacity: 0.15; pointer-events: none;
        }

        .card-form {
            background: white; border-radius: 12px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); padding: 2rem;
        }

        .form-label { font-weight: 600; color: var(--komdigi-blue-dark); }
        .form-control:focus, .form-select:focus {
            border-color: var(--komdigi-blue-light);
            box-shadow: 0 0 0 0.25rem rgba(0, 174, 239, 0.15);
        }

        .btn-save {
            background-color: var(--komdigi-blue-dark); color: white; padding: 10px 25px;
            border-radius: 8px; font-weight: 600; border: none; transition: 0.3s;
        }
        .btn-save:hover { background-color: #0a2155; transform: translateY(-2px); }

        .btn-cancel {
            background-color: white; color: #64748b; border: 1px solid #cbd5e1;
            padding: 10px 25px; border-radius: 8px; font-weight: 600; transition: 0.3s;
        }
        .btn-cancel:hover { background-color: #f1f5f9; color: var(--komdigi-blue-dark); }
    </style>
</head>
<body class="p-4">
    <div class="container" style="max-width: 800px;">

        <div class="header-section text-center">
            <h2 class="fw-bold mb-1"><i class="fas fa-plus-circle me-2"></i>Tambah Laporan Tugas</h2>
            <p class="mb-0 opacity-75">Input data laporan tugas pegawai secara manual</p>
        </div>

        <div class="card-form">
            <form action="{{ route('admin.assignments.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="form-label">Nama Pegawai</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-user text-muted"></i></span>
                        <input type="text" name="nama_pegawai" class="form-control" placeholder="Masukkan nama lengkap..." required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Tanggal Kegiatan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="far fa-calendar-alt text-muted"></i></span>
                            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Status Laporan</label>
                        <select name="status" class="form-select">
                            <option value="1">✅ Sudah Dilaporkan</option>
                            <option value="0">⏳ Belum Dilaporkan</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Nama Kegiatan / Tugas</label>
                        {{-- 👇 TOMBOL AI AJAIB 👇 --}}
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3 fw-bold" id="btn-ai-enhance" style="font-size: 0.75rem; border-width: 2px;">
                            <i class="fas fa-magic me-1"></i> Rapihkan dengan AI
                        </button>
                    </div>
                    {{-- Pastikan ada id="kegiatan-text" biar terbaca oleh script --}}
                    <textarea id="kegiatan-text" name="kegiatan" class="form-control" rows="4" placeholder="Ketik laporan acak di sini, lalu klik 'Rapihkan dengan AI'..." required></textarea>
                    <small class="text-muted mt-1 d-block" id="ai-status">Ketik draf kasar, biarkan AI yang menyempurnakan bahasanya.</small>
                </div>

                <div class="mb-4">
                    <label class="form-label">Link Bukti Laporan (Opsional)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fas fa-link text-muted"></i></span>
                        <input type="text" name="link_bukti" class="form-control" placeholder="https://drive.google.com/...">
                    </div>
                    <div class="form-text text-muted">Biarkan kosong atau isi tanda strip (-) jika belum ada bukti.</div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.assignments') }}" class="btn btn-cancel">Batal</a>
                    <button type="submit" class="btn btn-save"><i class="fas fa-save me-2"></i>Simpan Data</button>
                </div>

                

            </form>
        </div>
    </div>

    {{-- 👇 SCRIPT UNTUK TOMBOL AI 👇 --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#btn-ai-enhance').click(function() {
                let text = $('#kegiatan-text').val();
                let btn = $(this);
                let status = $('#ai-status');

                if(text.trim() === '') {
                    alert('Ketik draf laporannya dulu ya Min, baru AI bisa bantu rapihkan!');
                    return;
                }

                // Ubah status tombol saat loading
                let originalBtnText = btn.html();
                btn.html('<i class="fas fa-spinner fa-spin me-1"></i> AI sedang bekerja...');
                btn.prop('disabled', true);
                status.text('AI sedang merangkai kata-kata birokrasi yang cantik...');
                status.addClass('text-primary fw-bold').removeClass('text-muted text-danger text-success');

                $.ajax({
                    url: '{{ route("ai.enhance") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        text: text
                    },
                    success: function(response) {
                        if(response.result) {
                            $('#kegiatan-text').val(response.result); // Timpa text area dengan hasil AI
                            status.text('✨ Taraa! Laporan berhasil dirapihkan oleh AI.');
                            status.addClass('text-success').removeClass('text-primary');
                        }
                    },
                    error: function(xhr) {
                        // 👇 INI YANG KITA UBAH BIAR ERROR ASLINYA KELIATAN 👇
                        let pesanError = "Terjadi kesalahan.";
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            pesanError = xhr.responseJSON.error;
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            pesanError = xhr.responseJSON.message;
                        }

                        status.html('<i class="fas fa-exclamation-triangle"></i> Gagal: ' + pesanError);
                        status.addClass('text-danger').removeClass('text-primary text-success');
                        console.log("Detail Error:", xhr);
                    },
                    complete: function() {
                        btn.html(originalBtnText); // Kembalikan tombol ke semula
                        btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>
