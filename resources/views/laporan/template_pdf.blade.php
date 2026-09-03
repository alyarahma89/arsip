<!DOCTYPE html>
<html>
<head>
    <title>Laporan Kegiatan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; line-height: 1.6; font-size: 14px; }
        .header { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .content-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .content-table td { padding: 8px; vertical-align: top; }
        .signature { width: 100%; margin-top: 50px; }
        .signature td { text-align: center; width: 50%; }
    </style>
</head>
<body>
    <div class="header">
        <h3>KEMENTERIAN KOMUNIKASI DAN DIGITAL REPUBLIK INDONESIA</h3>
        <h2>BALAI BESAR PENGEMBANGAN SDM DAN PENELITIAN</h2>
        <p>Jl. P. Kemerdekaan, Makassar, Sulawesi Selatan</p>
    </div>

    <h4 style="text-align: center; text-decoration: underline;">LAPORAN PELAKSANAAN KEGIATAN</h4>

    <p>Berdasarkan rekapitulasi data pada sistem arsip BBPSDMP Komdigi, berikut adalah rincian pelaksanaan kegiatan yang telah dilaksanakan:</p>

    <table class="content-table">
        <tr>
            <td width="30%"><strong>Nama Kegiatan</strong></td>
            <td width="5%">:</td>
            <td>{{ $kegiatan->nama_kegiatan }}</td>
        </tr>
        <tr>
            <td><strong>Akademi</strong></td>
            <td>:</td>
            <td>{{ $kegiatan->akademi }}</td>
        </tr>
        <tr>
            <td><strong>Tanggal Pelaksanaan</strong></td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td><strong>Lokasi Kegiatan</strong></td>
            <td>:</td>
            <td>{{ $kegiatan->lokasi }}</td>
        </tr>
        <tr>
            <td><strong>Kategori Arsip</strong></td>
            <td>:</td>
            <td>{{ $statusArsip }}</td>
        </tr>
        <tr>
            <td><strong>Susunan Panitia</strong></td>
            <td>:</td>
            <td>{{ $kegiatan->panitia }}</td>
        </tr>
    </table>

    <table class="signature">
        <tr>
            <td></td>
            <td>
                Mengetahui,<br>
                Penanggung Jawab Kegiatan<br><br><br><br><br>
                <strong><u>{{ $kegiatan->penanggung_jawab }}</u></strong>
            </td>
        </tr>
    </table>
</body>
</html>
