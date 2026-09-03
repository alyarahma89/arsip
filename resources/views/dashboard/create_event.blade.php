<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kegiatan Baru - Komdigi</title>
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

        .btn-header {
            background: rgba(255, 255, 255, 0.2); color: white; border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px); transition: 0.3s; font-weight: 500;
        }
        .btn-header:hover { background: white; color: var(--komdigi-blue-dark); transform: translateY(-2px); }

        .card-form {
            background: white; border-radius: 12px; border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); padding: 2rem; height: 100%;
        }

        .form-label { font-weight: 600; color: var(--komdigi-blue-dark); font-size: 0.9rem; }
        .form-control:focus, .form-select:focus {
            border-color: var(--komdigi-blue-light); box-shadow: 0 0 0 0.25rem rgba(0, 174, 239, 0.15);
        }

        .btn-save {
            background-color: var(--komdigi-blue-dark); color: white; padding: 12px 40px;
            border-radius: 50px; font-weight: 600; border: none; transition: 0.3s;
            box-shadow: 0 4px 15px rgba(14, 44, 108, 0.3);
        }
        .btn-save:hover { background-color: #0a2155; transform: translateY(-3px); box-shadow: 0 6px 20px rgba(14, 44, 108, 0.4); }

        /* Style untuk Area Scan */
        .scan-area {
            background: linear-gradient(135deg, #0E2C6C 0%, #00AEEF 100%);
            color: white; border-radius: 15px; margin-bottom: 2rem;
        }
    </style>
</head>
<body class="p-4">
    <div class="container" style="max-width: 1100px;">

        <div class="header-section d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-plus-circle me-2"></i>Tambah Kegiatan Baru</h2>
                <p class="mb-0 opacity-75">Input data kegiatan DTS secara manual</p>
            </div>
            <a href="{{ route('admin.events') }}" class="btn btn-header btn-sm px-3 py-2 rounded-pill shadow-sm text-decoration-none">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fs-4 me-3"></i>
                <div>
                    <strong>Gagal Menyimpan!</strong> Silakan periksa inputan berikut:
                    <ul class="mb-0 mt-1 small">
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card scan-area border-0 shadow-sm">
            <div class="card-body text-center p-4">
                <div class="mb-3">
                    <i class="fas fa-magic fs-1 mb-2" style="color: #FDB913;"></i>
                    <h5 class="fw-bold mb-1">Smart Fill via Kamera</h5>
                    <p class="small opacity-75">Foto dokumen fisik (Undangan/ST), AI akan mengisi form otomatis secara presisi.</p>
                </div>

                <input type="file" accept="image/*" capture="environment" id="scanInput" style="display: none;">

                <button type="button" class="btn btn-warning fw-bold px-4 rounded-pill shadow" onclick="document.getElementById('scanInput').click()">
                    <i class="fas fa-camera me-2"></i>Ambil Foto Dokumen
                </button>

                <div id="loadingScan" class="mt-4" style="display: none;">
                    <div class="spinner-grow text-warning" role="status" style="width: 1rem; height: 1rem;"></div>
                    <div class="spinner-grow text-light mx-2" role="status" style="width: 1rem; height: 1rem;"></div>
                    <div class="spinner-grow text-info" role="status" style="width: 1rem; height: 1rem;"></div>
                    <p class="small mt-2 mb-0 fw-bold" id="loadingText">Membaca teks...</p>
                </div>
            </div>
        </div>
        <form action="{{ route('events.store') }}" method="POST">
            @csrf

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card-form">
                        <h5 class="fw-bold mb-4 text-komdigi-blue border-bottom pb-3" style="color: var(--komdigi-blue-dark);">
                            <i class="fas fa-info-circle me-2"></i>Informasi Kegiatan
                        </h5>

                        <div class="mb-3">
                            <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" required placeholder="Contoh: Pelatihan Digital Marketing Batch 1" value="{{ old('nama_kegiatan') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pilih Akademi <span class="text-danger">*</span></label>
                            <select name="akademi" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Akademi --</option>
                                <option value="DEA" {{ old('akademi') == 'DEA' ? 'selected' : '' }}>DEA (Digital Entrepreneurship Academy)</option>
                                <option value="TA" {{ old('akademi') == 'TA' ? 'selected' : '' }}>TA (Thematic Academy)</option>
                                <option value="VSGA" {{ old('akademi') == 'VSGA' ? 'selected' : '' }}>VSGA (Vocational School Graduate Academy)</option>
                                <option value="GTA" {{ old('akademi') == 'GTA' ? 'selected' : '' }}>GTA (Government Transformation Academy)</option>
                                <option value="FGA" {{ old('akademi') == 'FGA' ? 'selected' : '' }}>FGA (Fresh Graduate Academy)</option>
                                <option value="TIK" {{ old('akademi') == 'TIK' ? 'selected' : '' }}>TIK (Teknologi Informasi & Komunikasi)</option>
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required value="{{ old('tanggal_mulai') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" name="tanggal_selesai" class="form-control" value="{{ old('tanggal_selesai') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Penanggung Jawab (PJ)</label>
                            <input type="text" name="penanggung_jawab" id="penanggung_jawab" class="form-control" placeholder="Nama PJ" value="{{ old('penanggung_jawab') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Panitia / Anggota</label>
                            <textarea name="panitia" class="form-control" rows="2" placeholder="Pisahkan dengan koma">{{ old('panitia') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="color: #10b981;"><i class="fab fa-google-drive me-1"></i> Link Folder Laporan DTS</label>
                            <input type="url" name="lokasi" id="lokasi" class="form-control" placeholder="https://drive.google.com/..." value="{{ old('lokasi') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" style="color: var(--komdigi-accent);"><i class="fas fa-file-alt me-1"></i> Link Pencatatan Arsip</label>
                            <input type="url" name="link_pencatatan" class="form-control" placeholder="https://forms.gle/..." value="{{ old('link_pencatatan') }}">
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="card-form bg-light border-0">
                        <h5 class="fw-bold mb-4 text-success border-bottom pb-3">
                            <i class="fas fa-check-circle me-2"></i>Status Kelengkapan Admin
                        </h5>

                        <div class="d-flex flex-column gap-3">
                            @php
                                $checks = [
                                    'adm_surat' => 'Surat Undangan / Tugas',
                                    'adm_dokumentasi' => 'Dokumentasi Foto',
                                    'adm_daftar_hadir' => 'Daftar Hadir',
                                    'adm_rundown' => 'Rundown Acara',
                                    'adm_notulensi' => 'Notulensi',
                                    'adm_laporan' => 'Laporan Akhir',
                                    'adm_materi_instruktur' => 'Materi Instruktur',
                                    'adm_materi_narasumber' => 'Materi Narasumber',
                                    'adm_release' => 'Press Release / Berita',
                                    'adm_sertifikat' => 'Sertifikat',
                                    'adm_lapgas' => 'Laporan Tugas (Lapgas)'
                                ];
                            @endphp

                            @foreach($checks as $key => $label)
                            <div class="bg-white p-3 rounded shadow-sm border">
                                <label class="form-label mb-2 d-block small text-muted text-uppercase fw-bold">{{ $label }}</label>
                                <select name="{{ $key }}" class="form-select form-select-sm fw-medium">
                                    <option value="BELUM" {{ old($key) == 'BELUM' ? 'selected' : '' }}>BELUM</option>
                                    <option value="BELUM LENGKAP" {{ old($key) == 'BELUM LENGKAP' ? 'selected' : '' }}>BELUM LENGKAP</option>
                                    <option value="SUDAH" {{ old($key) == 'SUDAH' ? 'selected' : '' }}>SUDAH</option>
                                    <option value="TIDAK ADA" {{ old($key) == 'TIDAK ADA' ? 'selected' : '' }}>TIDAK ADA</option>
                                </select>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 text-center mb-5">
                <button type="submit" class="btn btn-save btn-lg">
                    <i class="fas fa-save me-2"></i> Simpan Data Kegiatan
                </button>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script>
        // Inisialisasi PDF.js
        const pdfjsLib = window['pdfjs-dist/build/pdf'];
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // Ubah filter input agar bisa terima PDF juga
        document.getElementById('scanInput').setAttribute('accept', 'image/*,.pdf');

        document.getElementById('scanInput').addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const loadingDiv = document.getElementById('loadingScan');
            const loadingText = document.getElementById('loadingText');
            loadingDiv.style.display = 'block';

            try {
                let imageSource = file;

                // --- JIKA FILE ADALAH PDF ---
                if (file.type === "application/pdf") {
                    loadingText.innerText = "📄 Mengonversi PDF ke Gambar...";
                    const arrayBuffer = await file.arrayBuffer();
                    const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
                    const page = await pdf.getPage(1); // Ambil halaman 1 saja
                    const viewport = page.getViewport({ scale: 2 });
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    await page.render({ canvasContext: context, viewport: viewport }).promise;
                    imageSource = canvas.toDataURL('image/png'); // Hasil konversi PDF jadi gambar
                }

                // --- PROSES OCR (Sama seperti sebelumnya) ---
                loadingText.innerText = "🔍 Memindai teks (OCR)...";
                const result = await Tesseract.recognize(imageSource, 'ind');
                const rawText = result.data.text;

                if(!rawText.trim()) {
                    Swal.fire('Gagal', 'Teks dokumen tidak terbaca. Pastikan file jelas!', 'error');
                    loadingDiv.style.display = 'none';
                    return;
                }

                // --- KIRIM KE AI ARAI ---
                loadingText.innerText = "🤖 Arai AI sedang merapikan data...";
                const response = await axios.post("{{ route('api.parse.ocr') }}", { text: rawText });
                const dataOcr = response.data;

                if (dataOcr) {
                    if(dataOcr.nama_kegiatan) document.getElementById('nama_kegiatan').value = dataOcr.nama_kegiatan;
                    if(dataOcr.lokasi) document.getElementById('lokasi').value = dataOcr.lokasi;
                    if(dataOcr.tanggal_mulai) document.getElementById('tanggal_mulai').value = dataOcr.tanggal_mulai;
                    if(dataOcr.penanggung_jawab) document.getElementById('penanggung_jawab').value = dataOcr.penanggung_jawab;

                    // Auto-Select Akademi
                    if(dataOcr.akademi) {
                        const sel = document.querySelector('select[name="akademi"]');
                        for (let i = 0; i < sel.options.length; i++) {
                            if (sel.options[i].value === dataOcr.akademi.toUpperCase()) {
                                sel.selectedIndex = i; break;
                            }
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data dari ' + (file.type === "application/pdf" ? "PDF" : "Foto") + ' telah disalin ke form.',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }

                loadingText.innerText = "✅ Selesai!";
                setTimeout(() => { loadingDiv.style.display = 'none'; }, 2000);

            } catch (error) {
                console.error("Error Scan:", error);
                Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
                loadingDiv.style.display = 'none';
            }
        });
        </script>
        </body>
</html>
