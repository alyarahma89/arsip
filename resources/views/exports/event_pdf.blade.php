<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kegiatan DTS</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header h3, .header p { margin: 0; padding: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; vertical-align: top; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI KEGIATAN DIGITAL TALENT SCHOLARSHIP (DTS)</h2>
        <h3>KEMENTERIAN KOMUNIKASI DAN DIGITAL (KOMDIGI)</h3>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 10%;">Akademi</th>
                <th style="width: 25%;">Nama Kegiatan</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 15%;">Penanggung Jawab</th>
                <th style="width: 30%;">Anggota / Panitia</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($events as $data)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ $data->akademi ?? '-' }}</td>
                <td>{{ $data->nama_kegiatan }}</td>
                <td class="text-center">{{ $data->tanggal_mulai ? \Carbon\Carbon::parse($data->tanggal_mulai)->translatedFormat('d M Y') : '-' }}</td>
                <td>{{ $data->penanggung_jawab ?? '-' }}</td>
                <td>{{ $data->panitia ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Tidak ada data kegiatan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
