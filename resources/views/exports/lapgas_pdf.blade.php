<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Tugas Pegawai</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3, .header p { margin: 0; padding: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI LAPORAN TUGAS PEGAWAI</h2>
        <h3>KEMENTERIAN KOMUNIKASI DAN DIGITAL (KOMDIGI)</h3>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Pegawai</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 35%;">Kegiatan</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Link Bukti</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($assignments as $data)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $data->nama_pegawai }}</td>
                <td class="text-center">{{ $data->tanggal ? \Carbon\Carbon::parse($data->tanggal)->translatedFormat('d M Y') : '-' }}</td>
                <td>{{ $data->kegiatan }}</td>
                <td class="text-center">{{ $data->status_laporan ? 'Selesai' : 'Pending' }}</td>
                <td>{{ $data->link_bukti }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data laporan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
