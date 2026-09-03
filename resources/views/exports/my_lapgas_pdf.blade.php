<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tugas Saya</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3, .header p { margin: 0; padding: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; /* Mencegah tabel melebar */ }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; word-wrap: break-word; /* Teks panjang akan otomatis turun */ }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI TUGAS INDIVIDU</h2>
        <h3>Nama Pegawai: {{ Auth::user()->name }}</h3>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 45%;">Kegiatan</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;">Status</th>
                <th style="width: 20%;">Link Bukti</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($assignments as $data)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $data->kegiatan }}</td>
                <td class="text-center">{{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d M Y') : '-' }}</td>
                <td class="text-center">{{ $data->status_laporan ? 'Selesai' : 'Pending' }}</td>

                {{-- 👇 INI BAGIAN YANG DIPERBAIKI MIN 👇 --}}
                <td class="text-center">
                    @php
                        // Cek apakah isinya benar-benar link URL
                        $isUrl = filter_var($data->link_bukti, FILTER_VALIDATE_URL);
                    @endphp

                    @if($isUrl)
                        <a href="{{ $data->link_bukti }}" style="color: blue; text-decoration: underline;">Buka Tautan</a>
                    @else
                        {{ $data->link_bukti ? $data->link_bukti : '-' }}
                    @endif
                </td>
                {{-- ☝️ ================================ ☝️ --}}

            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada tugas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
