<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Kegiatan DTS</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; } /* Font sedikit lebih kecil agar muat banyak kolom */
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2, .header p { margin: 0; padding: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; table-layout: fixed; /* Kunci rahasia anti-melebar */ }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; word-wrap: break-word; /* Teks turun otomatis */ vertical-align: top; }
        th { background-color: #f2f2f2; text-align: center; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h2>REKAPITULASI DATA KEGIATAN DTS</h2>
        <p>BBPSDMP Komdigi</p>
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
                <th style="width: 15%;">Lokasi</th>
                <th style="width: 15%;">Link Arsip</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @forelse($events as $event)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td class="text-center">{{ $event->akademi ? $event->akademi : '-' }}</td>
                <td>{{ $event->nama_kegiatan }}</td>
                <td class="text-center">{{ $event->tanggal_mulai ? \Carbon\Carbon::parse($event->tanggal_mulai)->translatedFormat('d M Y') : '-' }}</td>
                <td>{{ $event->penanggung_jawab }}</td>

                {{-- 👇 CEK LOKASI (SIAPA TAHU ISINYA LINK DRIVE) 👇 --}}
                <td class="text-center">
                    @php $isUrlLokasi = filter_var($event->lokasi, FILTER_VALIDATE_URL); @endphp
                    @if($isUrlLokasi)
                        <a href="{{ $event->lokasi }}" style="color: blue; text-decoration: underline;">Buka Maps/Drive</a>
                    @else
                        {{ $event->lokasi ? $event->lokasi : '-' }}
                    @endif
                </td>

                {{-- 👇 CEK LINK PENCATATAN / ARSIP 👇 --}}
                <td class="text-center">
                    @php $isUrlArsip = filter_var($event->link_pencatatan, FILTER_VALIDATE_URL); @endphp
                    @if($isUrlArsip)
                        <a href="{{ $event->link_pencatatan }}" style="color: blue; text-decoration: underline;">Buka Folder</a>
                    @else
                        {{ $event->link_pencatatan && $event->link_pencatatan != '-' ? $event->link_pencatatan : 'Belum Ada' }}
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Belum ada data kegiatan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
