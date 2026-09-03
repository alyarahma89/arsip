<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan DTS</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; line-height: 1.6; color: #333; }
        .kop-surat { text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; }
        .title { text-align: center; text-decoration: underline; font-weight: bold; font-size: 16px; margin-bottom: 20px; }
        .info-event { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; text-align: center; }
        .status-sudah { color: green; font-weight: bold; text-align: center; }
        .status-belum { color: red; font-weight: bold; text-align: center; }
        .footer { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h2 style="margin:0;">INSTANSI DIGITAL ARCHIVE</h2>
        <p style="margin:0;">Sistem Manajemen Arsip Terintegrasi Google Drive</p>
    </div>

    <div class="title">LAPORAN EVALUASI KELENGKAPAN ADMINISTRASI</div>

    <div class="info-event">
        <strong>Nama Kegiatan :</strong> {{ $event->judul }} <br>
        <strong>Lokasi Drive  :</strong> {{ $event->lokasi }} <br>
        <strong>Progres       :</strong> {{ $percentage }}%
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="60%">Uraian Dokumen</th>
                <th width="30%">Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($columns as $col => $label)
            <tr>
                <td align="center">{{ $no++ }}</td>
                <td>{{ $label }}</td>
                <td class="{{ $event->$col == 'SUDAH' ? 'status-sudah' : 'status-belum' }}">
                    {{ $event->$col }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ $date }} <br>
        <br><br><br>
        <strong>Administrator Sistem</strong>
    </div>
</body>
</html>
